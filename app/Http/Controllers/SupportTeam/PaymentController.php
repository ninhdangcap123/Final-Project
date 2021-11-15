<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\GetPaymentHelper;
use App\Helpers\GetSystemInfoHelper;
use App\Helpers\JsonHelper;
use App\Helpers\RouteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\PaymentCreate;
use App\Http\Requests\Payment\PaymentUpdate;
use App\Http\Requests\Payment\PaymentNow;

use App\Http\Requests\Payment\PaymentSelectClass;
use App\Models\Setting;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;

use App\Repositories\Payment\PaymentRepositoryInterface;
use App\Repositories\PaymentRecord\PaymentRecordRepositoryInterface;

use App\Repositories\Receipt\ReceiptRepositoryInterface;
use App\Repositories\Student\StudentRepositoryInterface;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PDF;

class PaymentController extends Controller
{
    protected $myCourseRepo;
    protected $paymentRecordRepo;
    protected $receiptRepo;
    protected $paymentRepo;
    protected $studentRepo;
    protected $year;

    public function __construct(
        PaymentRecordRepositoryInterface $paymentRecordRepo,
        ReceiptRepositoryInterface       $receiptRepo,
        MyCourseRepositoryInterface      $myCourseRepo,
        PaymentRepositoryInterface       $paymentRepo,
        StudentRepositoryInterface       $studentRepo
    )
    {
        $this->myCourseRepo = $myCourseRepo;
        $this->paymentRepo = $paymentRepo;
        $this->receiptRepo = $receiptRepo;
        $this->paymentRecordRepo = $paymentRecordRepo;
        $this->year = GetSystemInfoHelper::getCurrentSession();
        $this->studentRepo = $studentRepo;
        $this->middleware('teamAccount');
    }

    public function index()
    {
        $data['selected'] = false;
        $data['years'] = $this->paymentRepo->getPaymentYears();

        return view('pages.support_team.payments.index', $data);
    }

    public function show($year)
    {
        $data['payments'] = $payment = $this->paymentRepo->getPayment([ 'year' => $year ])->get();

        if( ( $payment->count() < 1 ) ) {
            return RouteHelper::goWithDanger('payments.index');
        }

        $data['selected'] = true;
        $data['my_courses'] = $this->myCourseRepo->getAll();
        $data['years'] = $this->paymentRepo->getPaymentYears();
        $data['year'] = $year;

        return view('pages.support_team.payments.index', $data);

    }

    public function selectYear(Request $request)
    {
        return RouteHelper::goToRoute([ 'payments.show', $request->year ]);
    }

    public function create()
    {
        $data['my_courses'] = $this->myCourseRepo->getAll();
        return view('pages.support_team.payments.create', $data);
    }

    public function invoice($student_id, $year = NULL)
    {
        if( !$student_id ) {
            return RouteHelper::goWithDanger();
        }

        $invoice = $year ? $this->paymentRecordRepo->getAllMyPR($student_id, $year) : $this->paymentRecordRepo->getAllMyPR($student_id);

        $data['sr'] = $this->studentRepo->findByUserId($student_id)->first();
        $paymentRecord = $invoice->get();
        $data['uncleared'] = $paymentRecord->where('paid', 0);
        $data['cleared'] = $paymentRecord->where('paid', 1);

        return view('pages.support_team.payments.invoice', $data);
    }

    public function receipts($pr_id)
    {
        if( !$pr_id ) {
            return RouteHelper::goWithDanger();
        }

        try {
            $data['pr'] = $paymentRecord = $this->paymentRecordRepo->getRecord([ 'id' => $pr_id ])->with('receipt')->first();
        } catch( ModelNotFoundException $ex ) {
            return back()->with('flash_danger', __('msg.rnf'));
        }
        $data['receipts'] = $paymentRecord->receipt;
        $data['payment'] = $paymentRecord->payment;
        $data['sr'] = $this->studentRepo->findByUserId($paymentRecord->student_id)->first();
        $data['s'] = Setting::all()->flatMap(function ($s) {
            return [ $s->type => $s->description ];
        });

        return view('pages.support_team.payments.receipt', $data);
    }

    public function pdfReceipts($pr_id)
    {
        if( !$pr_id ) {
            return RouteHelper::goWithDanger();
        }

        try {
            $data['pr'] = $paymentRecord = $this->paymentRecordRepo->getRecord([ 'id' => $pr_id ])->with('receipt')->first();
        } catch( ModelNotFoundException $ex ) {
            return back()->with('flash_danger', __('msg.rnf'));
        }
        $data['receipts'] = $paymentRecord->receipt;
        $data['payment'] = $paymentRecord->payment;
        $data['sr'] = $sr = $this->studentRepo->findByUserId($paymentRecord->student_id)->first();
        $data['s'] = Setting::all()->flatMap(function ($s) {
            return [ $s->type => $s->description ];
        });

        $pdfName = 'Receipt_'.$paymentRecord->ref_no;

        return PDF::loadView('pages.support_team.payments.receipt', $data)->download($pdfName);

        //return $this->downloadReceipt('pages.support_team.payments.receipt', $d, $pdf_name);
    }

    public function payNow(PaymentNow $request, $pr_id)
    {
        $data = $request->validated();
        $paymentRecord = $this->paymentRecordRepo->find($pr_id);
        $payment = $this->paymentRepo->find($paymentRecord->payment_id);
        $data['amt_paid'] = $amount = $paymentRecord->amt_paid + $request->amt_paid;
        $data['balance'] = $balance = $payment->amount - $amount;
        $data['paid'] = $balance < 1 ? 1 : 0;

        $this->paymentRecordRepo->update($pr_id, $data);
        $data2['amt_paid'] = $request->amt_paid;
        $data2['balance'] = $balance;
        $data2['pr_id'] = $pr_id;
        $data2['year'] = $this->year;

        $this->receiptRepo->create($data2);
        return JsonHelper::jsonUpdateSuccess();
    }

    public function manage($course_id = NULL)
    {
        $data['my_courses'] = $this->myCourseRepo->getAll();
        $data['selected'] = false;

        if( $course_id ) {
            $data['students'] = $students = $this->studentRepo->getRecord([ 'my_course_id' => $course_id ])->get()->sortBy('user.name');
            if( $students->count() < 1 ) {
                return RouteHelper::goWithDanger('payments.manage');
            }
            $data['selected'] = true;
            $data['my_course_id'] = $course_id;
        }

        return view('pages.support_team.payments.manage', $data);
    }

    public function selectClass(PaymentSelectClass $request)
    {
        $where = $request->validated();

        $where['my_course_id'] = $course_id = $request->my_course_id;

        $payment1 = $this->paymentRepo->getPayment([
            'my_course_id' => $course_id,
            'year' => $this->year
        ])->get();
        $payment2 = $this->paymentRepo->getGeneralPayment([ 'year' => $this->year ])->get();
        $payments = $payment2->count() ? $payment1->merge($payment2) : $payment1;
        $students = $this->studentRepo->getRecord($where)->get();

        if( $payments->count() && $students->count() ) {
            foreach( $payments as $payment ) {
                foreach( $students as $student ) {
                    $paymentRecord['student_id'] = $student->user_id;
                    $paymentRecord['payment_id'] = $payment->id;
                    $paymentRecord['year'] = $this->year;
                    $receipt = $this->paymentRecordRepo->create($paymentRecord);
                    $receipt->ref_no ?: $receipt->update([ 'ref_no' => mt_rand(100000, 99999999) ]);

                }
            }
        }
        return RouteHelper::goToRoute([ 'payments.manage', $course_id ]);
    }

    public function store(PaymentCreate $request)
    {
        $data = $request->validated();
        $data['year'] = $this->year;
        $data['ref_no'] = GetPaymentHelper::genRefCode();
        $this->paymentRepo->create($data);

        return JsonHelper::jsonStoreSuccess();
    }

    public function edit($id)
    {
        $data['payment'] = $pay = $this->paymentRepo->find($id);
        return is_null($pay) ? RouteHelper::goWithDanger('payments.index') : view('pages.support_team.payments.edit', $data);
    }

    public function update(PaymentUpdate $request, $id)
    {
        $data = $request->validated();
        $this->paymentRepo->update($id, $data);
        return JsonHelper::jsonUpdateSuccess();
    }

    public function destroy($id)
    {
        $this->paymentRepo->find($id)->delete();
        return RouteHelper::deleteOk('payments.index');
    }

    public function reset_record($id)
    {
        $paymentRecord['amt_paid'] = $paymentRecord['paid'] = $paymentRecord['balance'] = 0;
        $this->paymentRecordRepo->update($id, $paymentRecord);
        $this->receiptRepo->delete([ 'pr_id' => $id ]);
        return back()->with('flash_success', __('msg.update_ok'));
    }

    protected function downloadReceipt($page, $data, $name = NULL)
    {
        $path = 'receipts/file.html';
        $disk = Storage::disk('local');
        $disk->put($path, view($page, $data));
        $html = $disk->get($path);
        return PDF::loadHTML($html)->download($name);
    }
}

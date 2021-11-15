<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\CheckUsersHelper;
use App\Helpers\DisplayMessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pin\PinCreate;
use App\Http\Requests\Pin\PinVerify;
use App\Repositories\Pin\PinRepositoryInterface;
use App\Repositories\PinRepo;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PinController extends Controller
{
    protected $pinRepo;
    protected $examIsLocked;
    protected $userRepo;

    public function __construct(
        PinRepositoryInterface  $pinRepo,
        UserRepositoryInterface $userRepo
    )
    {
        $this->pinRepo = $pinRepo;
        $this->userRepo = $userRepo;
        $this->middleware('examIsLocked');
        $this->middleware('teamSA', [ 'except' => [ 'verify', 'enter_pin' ] ]);
    }

    public function index()
    {
        $data['pin_count'] = $this->pinRepo->countValid();
        $data['valid_pins'] = $this->pinRepo->getValid();
        $data['used_pins'] = $this->pinRepo->getInValid();

        return view('pages.support_team.pins.index', $data);
    }

    public function create()
    {
        if( $this->pinRepo->countValid() > 500 ) {
            return redirect()->route('pins.index')->with('flash_danger', __('msg.pin_max'));
        }
        return view('pages.support_team.pins.create');
    }

    public function enterPin($student_id)
    {
        if( CheckUsersHelper::userIsTeamSA() ) {
            return redirect(route('dashboard'));
        }

        if( $this->checkPinVerified($student_id) ) {
            return Session::has('marks_url') ? redirect(Session::get('marks_url')) : redirect()->route('dashboard');
        }
        $data['student'] = $this->userRepo->find($student_id);
        return view('pages.support_team.pins.enter', $data);
    }

    protected function checkPinVerified($st_id)
    {
        return Session::has('pin_verified') && Session::get('pin_verified') == $st_id;
    }

    public function verify(PinVerify $request, $student_id)
    {
        $user = Auth::user();
        $code = $this->pinRepo->findValidCode($request->pin_code);
        if( $code->count() < 1 ) {
            $code = $this->pinRepo->getUserPin($request->pin_code, $user->id, $student_id);
        }
        if( $code->count() > 0 && $code->first()->times_used < 6 ) {
            $code = $code->first();
            $data['times_used'] = $code->times_used + 1;
            $data['user_id'] = $user->id;
            $data['student_id'] = $student_id;
            $data['user_type'] = $user->user_type;
            $data['used'] = 1;
            $this->pinRepo->update($code->id, $data);
            Session::put('pin_verified', $student_id);
            return Session::has('marks_url') ? redirect(Session::get('marks_url')) : redirect()->route('dashboard');
        }

        return redirect()->route('pins.enter', DisplayMessageHelper::hash($student_id))->with('flash_danger', __('msg.pin_fail'));
    }

    public function store(PinCreate $request)
    {
        $pinCount = $request->pin_count;

        $data = [];
        for( $i = 0; $i < $pinCount; $i++ ) {
            $code = Str::random(5).'-'.Str::random(5).'-'.Str::random(6);
            $data[] = [ 'code' => strtoupper($code) ];
        }
        $this->pinRepo->insert($data);
        return redirect()->route('pins.index')->with('flash_success', __('msg.pin_create'));
    }

    public function destroy()
    {
        $this->pinRepo->deleteUsed();
        return back()->with('flash_success', 'Pins Deleted Successfully');
    }

}

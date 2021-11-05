<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\GetSystemInfoHelper;
use App\Helpers\JsonHelper;
use App\Helpers\Qs;
use App\Http\Requests\TimeTable\TSRequest;
use App\Http\Requests\TimeTable\TTRecordRequest;
use App\Http\Requests\TimeTable\TTRequest;
use App\Models\Setting;
use App\Repositories\Exam\ExamRepositoryInterface;
use App\Repositories\ExamRepo;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;
use App\Repositories\Subject\SubjectRepositoryInterface;
use App\Repositories\TimeSlot\TimeSlotRepositoryInterface;
use App\Repositories\TimeTable\TimeTableRepositoryInterface;
use App\Repositories\TimeTableRecord\TimeTableRecordRepositoryInterface;
use App\Repositories\TimeTableRepo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TimeTableController extends Controller
{
    protected $tt, $ttr, $ts , $my_course, $exam, $year, $subject;

    public function __construct(TimeSlotRepositoryInterface $ts, TimeTableRecordRepositoryInterface $ttr, TimeTableRepositoryInterface $tt,
                                SubjectRepositoryInterface $subject, MyCourseRepositoryInterface $my_course, ExamRepositoryInterface $exam)
    {
        $this->tt = $tt;
        $this->ts = $ts;
        $this->ttr = $ttr;
        $this->my_course = $my_course;
        $this->exam = $exam;
        $this->subject = $subject;
        $this->year = GetSystemInfoHelper::getCurrentSession();
    }

    public function index()

    {
        $d['exams'] = $this->exam->getExam(['year' => $this->year]);
        $d['my_courses'] = $this->my_course->getAll();
        $d['tt_records'] = $this->ttr->getAll();

        return view('pages.support_team.timetables.index', $d);
    }

    public function manage($ttr_id)
    {
        $d['ttr_id'] = $ttr_id;
        $d['ttr'] = $ttr = $this->ttr->find($ttr_id);
        $d['time_slots'] = $this->ts->getTimeSlotByTTR($ttr_id);
        $d['ts_existing'] = $this->ts->getExistingTS($ttr_id);
        $d['subjects'] = $this->subject->getSubject(['my_course_id' => $ttr->my_course_id])->get();
        $d['my_course'] = $this->my_course->find($ttr->my_course_id);

        if($ttr->exam_id){
            $d['exam_id'] = $ttr->exam_id;
            $d['exam'] = $this->exam->find($ttr->exam_id);
        }

        $d['tts'] = $this->tt->getTimeTable(['ttr_id' => $ttr_id]);

        return view('pages.support_team.timetables.manage', $d);
    }

    public function store(TTRequest $req)
    {
        $data = $req->all();
        $tms = $this->ts->find($req->ts_id);
        $d_date = $req->exam_date ?? $req->day;
        $data['timestamp_from'] = strtotime($d_date.' '.$tms->time_from);
        $data['timestamp_to'] = strtotime($d_date.' '.$tms->time_to);

        $this->tt->create($data);

        return JsonHelper::jsonStoreOk();
    }

    public function update(TTRequest $req, $tt_id)
    {
        $data = $req->all();
        $tms = $this->ts->find($req->ts_id);
        $d_date = $req->exam_date ?? $req->day;
        $data['timestamp_from'] = strtotime($d_date.' '.$tms->time_from);
        $data['timestamp_to'] = strtotime($d_date.' '.$tms->time_to);

        $this->tt->update($tt_id, $data);

        return back()->with('flash_success', __('msg.update_ok'));

    }

    public function delete($tt_id)
    {
        $this->tt->delete($tt_id);
        return back()->with('flash_success', __('msg.delete_ok'));
    }

    /*********** TIME SLOTS *************/

    public function storeTimeSlot(TSRequest $req)
    {
        $data = $req->all();
        $data['time_from'] = $tf =$req->hour_from.':'.$req->min_from.' '.$req->meridian_from;
        $data['time_to'] = $tt = $req->hour_to.':'.$req->min_to.' '.$req->meridian_to;
        $data['timestamp_from'] = strtotime($tf);
        $data['timestamp_to'] = strtotime($tt);
        $data['full'] = $tf.' - '.$tt;

        if($tf == $tt){
            return response()->json(['msg' => __('msg.invalid_time_slot'), 'ok' => FALSE]);
        }

        $this->ts->create($data);
        return JsonHelper::jsonStoreOk();
    }

    public function useTimeSlot(Request $req, $ttr_id)
    {
        $this->validate($req, ['ttr_id' => 'required'], [], ['ttr_id' => 'TimeTable Record']);

        $d = [];  //  Empty Current Time Slot Before Adding New
        $this->ts->deleteTimeSlotByIDs(['ttr_id' => $ttr_id]);
        $time_slots = $this->tt->getTimeSlotByTTR($req->ttr_id)->toArray();

        foreach($time_slots as $ts){
            $ts['ttr_id'] = $ttr_id;
            $this->ts->create($ts);
        }

        return redirect()->route('ttr.manage', $ttr_id)->with('flash_success', __('msg.update_ok'));

    }

    public function editTimeSlot($ts_id)
    {
        $d['tms'] = $this->ts->find($ts_id);
        return view('pages.support_team.timetables.time_slots.edit', $d);
    }

    public function updateTimeSlot(TSRequest $req, $ts_id)
    {
        $data = $req->all();
        $data['time_from'] = $tf =$req->hour_from.':'.$req->min_from.' '.$req->meridian_from;
        $data['time_to'] = $tt = $req->hour_to.':'.$req->min_to.' '.$req->meridian_to;
        $data['timestamp_from'] = strtotime($tf);
        $data['timestamp_to'] = strtotime($tt);
        $data['full'] = $tf.' - '.$tt;

        if($tf == $tt){
            return back()->with('flash_danger', __('msg.invalid_time_slot'));
        }

        $this->ts->update($ts_id, $data);
        return redirect()->route('ttr.manage', $req->ttr_id)->with('flash_success', __('msg.update_ok'));
    }

    public function deleteTimeSlot($ts_id)
    {
        $this->ts->delete($ts_id);
        return back()->with('flash_success', __('msg.delete_ok'));
    }


    /*********** RECORDS *************/

    public function editRecord($ttr_id)
    {
        $d['ttr'] = $ttr = $this->ttr->find($ttr_id);
        $d['exams'] = $this->exam->getExam(['year' => $ttr->year]);
        $d['my_courses'] = $this->my_course->getAll();

        return view('pages.support_team.timetables.edit', $d);
    }

    public function showRecord($ttr_id)
    {
        $d_time = [];
        $d['ttr'] = $ttr = $this->ttr->find($ttr_id);
        $d['ttr_id'] = $ttr_id;
        $d['my_course'] = $this->my_course->find($ttr->my_course_id);

        $d['time_slots'] = $tms = $this->tt->getTimeSlotByTTR($ttr_id);
        $d['tts'] = $tts = $this->tt->getTimeTable(['ttr_id' => $ttr_id]);

        if($ttr->exam_id){
            $d['exam_id'] = $ttr->exam_id;
            $d['exam'] = $this->exam->find($ttr->exam_id);
            $d['days'] = $days = $tts->unique('exam_date')->pluck('exam_date');
            $d_date = 'exam_date';
        }

        else{
            $d['days'] = $days = $tts->unique('day')->pluck('day');
            $d_date = 'day';
        }

        foreach ($days as $day) {
            foreach ($tms as $tm) {
                $d_time[] = ['day' => $day, 'time' => $tm->full, 'subject' => $tts->where('ts_id', $tm->id)->where($d_date, $day)->first()->subject->name ?? NULL ];
            }
        }

        $d['d_time'] = collect($d_time);

        return view('pages.support_team.timetables.show', $d);
    }
    public function printRecord($ttr_id)
    {
        $d_time = [];
        $d['ttr'] = $ttr = $this->ttr->find($ttr_id);
        $d['ttr_id'] = $ttr_id;
        $d['my_course'] = $this->my_course->find($ttr->my_course_id);

        $d['time_slots'] = $tms = $this->tt->getTimeSlotByTTR($ttr_id);
        $d['tts'] = $tts = $this->tt->getTimeTable(['ttr_id' => $ttr_id]);

        if($ttr->exam_id){
            $d['exam_id'] = $ttr->exam_id;
            $d['exam'] = $this->exam->find($ttr->exam_id);
            $d['days'] = $days = $tts->unique('exam_date')->pluck('exam_date');
            $d_date = 'exam_date';
        }

        else{
            $d['days'] = $days = $tts->unique('day')->pluck('day');
            $d_date = 'day';
        }

        foreach ($days as $day) {
            foreach ($tms as $tm) {
                $d_time[] = ['day' => $day, 'time' => $tm->full, 'subject' => $tts->where('ts_id', $tm->id)->where($d_date, $day)->first()->subject->name ?? NULL ];
            }
        }

        $d['d_time'] = collect($d_time);
        $d['s'] = Setting::all()->flatMap(function($s){
            return [$s->type => $s->description];
        });

        return view('pages.support_team.timetables.print', $d);
    }

    public function storeRecord(TTRecordRequest $req)
    {
        $data = $req->all();
        $data['year'] = $this->year;
        $this->ttr->create($data);

        return JsonHelper::jsonStoreOk();
    }

    public function updateRecord(TTRecordRequest $req, $id)
    {
        $data = $req->all();
        $this->ttr->update($id, $data);

        return JsonHelper::jsonUpdateOk();
    }

    public function deleteRecord($ttr_id)
    {
        $this->ttr->delete($ttr_id);
        return back()->with('flash_success', __('msg.delete_ok'));
    }
}

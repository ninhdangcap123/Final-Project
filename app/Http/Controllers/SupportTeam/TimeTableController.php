<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\GetSystemInfoHelper;
use App\Helpers\JsonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\TimeSlot;
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
use Illuminate\Http\Request;

class TimeTableController extends Controller
{
    protected $timeTableRepo;
    protected $timeTableRecordRepo;
    protected $timeSlotRepo;
    protected $myCourseRepo;
    protected $examRepo;
    protected $year;
    protected $subjectRepo;

    public function __construct(
        TimeSlotRepositoryInterface        $timeSlotRepo,
        TimeTableRecordRepositoryInterface $timeTableRecordRepo,
        TimeTableRepositoryInterface       $timeTableRepo,
        SubjectRepositoryInterface         $subjectRepo,
        MyCourseRepositoryInterface        $myCourseRepo,
        ExamRepositoryInterface            $examRepo
    )
    {
        $this->timeTableRepo = $timeTableRepo;
        $this->timeSlotRepo = $timeSlotRepo;
        $this->timeTableRecordRepo = $timeTableRecordRepo;
        $this->myCourseRepo = $myCourseRepo;
        $this->examRepo = $examRepo;
        $this->subjectRepo = $subjectRepo;
        $this->year = GetSystemInfoHelper::getCurrentSession();
    }

    public function index()

    {
        $data['exams'] = $this->examRepo->getExam([ 'year' => $this->year ]);
        $data['my_courses'] = $this->myCourseRepo->getAll();
        $data['tt_records'] = $this->timeTableRecordRepo->getAll();

        return view('pages.support_team.timetables.index', $data);
    }

    public function manage($ttr_id)
    {
        $data['ttr_id'] = $ttr_id;
        $data['ttr'] = $timeTableRecord = $this->timeTableRecordRepo->find($ttr_id);
        $data['time_slots'] = $this->timeSlotRepo->getTimeSlotByTTR($ttr_id);
        $data['ts_existing'] = $this->timeSlotRepo->getExistingTS($ttr_id);
        $data['subjects'] = $this->subjectRepo->getSubject([ 'my_course_id' => $timeTableRecord->my_course_id ])->get();
        $data['my_course'] = $this->myCourseRepo->find($timeTableRecord->my_course_id);

        if( $timeTableRecord->exam_id ) {
            $data['exam_id'] = $timeTableRecord->exam_id;
            $data['exam'] = $this->examRepo->find($timeTableRecord->exam_id);
        }

        $data['tts'] = $this->timeTableRepo->getTimeTable([ 'ttr_id' => $ttr_id ]);

        return view('pages.support_team.timetables.manage', $data);
    }

    public function store(TTRequest $request)
    {
        $data = $request->validated();
        $timeSlot = $this->timeSlotRepo->find($request->ts_id);
        $examDate = $request->exam_date ?? $request->day;
        $data['timestamp_from'] = strtotime($examDate.' '.$timeSlot->time_from);
        $data['timestamp_to'] = strtotime($examDate.' '.$timeSlot->time_to);

        $this->timeTableRepo->create($data);

        return JsonHelper::jsonStoreSuccess();
    }

    public function update(TTRequest $request, $tt_id)
    {
        $data = $request->validated();
        $timeSlot = $this->timeSlotRepo->find($request->ts_id);
        $examDate = $request->exam_date ?? $request->day;
        $data['timestamp_from'] = strtotime($examDate.' '.$timeSlot->time_from);
        $data['timestamp_to'] = strtotime($examDate.' '.$timeSlot->time_to);

        $this->timeTableRepo->update($tt_id, $data);

        return back()->with('flash_success', __('msg.update_ok'));

    }

    public function delete($tt_id)
    {
        $this->timeTableRepo->delete($tt_id);
        return back()->with('flash_success', __('msg.delete_ok'));
    }

    /*********** TIME SLOTS *************/

    public function storeTimeSlot(TSRequest $request)
    {
        $data = $request->validated();
        $data['time_from'] = $timeFrom = $request->hour_from.':'.$request->min_from.' '.$request->meridian_from;
        $data['time_to'] = $timeTo = $request->hour_to.':'.$request->min_to.' '.$request->meridian_to;
        $data['timestamp_from'] = strtotime($timeFrom);
        $data['timestamp_to'] = strtotime($timeTo);
        $data['full'] = $timeFrom.' - '.$timeTo;

        if( $timeFrom == $timeTo ) {
            return response()->json([ 'msg' => __('msg.invalid_time_slot'), 'ok' => FALSE ]);
        }

        $this->timeSlotRepo->create($data);
        return JsonHelper::jsonStoreSuccess();
    }

    public function useTimeSlot(TimeSlot $request, $ttr_id)
    {
        $timeSlot = $request->validated();

        $data = [];  //  Empty Current Time Slot Before Adding New
        $this->timeSlotRepo->deleteTimeSlotByIDs([ 'ttr_id' => $ttr_id ]);
        $timeSlots = $this->timeTableRepo->getTimeSlotByTTR($request->ttr_id)->toArray();

        foreach( $timeSlots as $timeSlot ) {
            $timeSlot['ttr_id'] = $ttr_id;
            $this->timeSlotRepo->create($timeSlot);
        }

        return redirect()->route('ttr.manage', $ttr_id)->with('flash_success', __('msg.update_ok'));

    }

    public function editTimeSlot($ts_id)
    {
        $data['tms'] = $this->timeSlotRepo->find($ts_id);
        return view('pages.support_team.timetables.time_slots.edit', $data);
    }

    public function updateTimeSlot(TSRequest $request, $ts_id)
    {
        $data = $request->validated();
        $data['time_from'] = $timeFrom = $request->hour_from.':'.$request->min_from.' '.$request->meridian_from;
        $data['time_to'] = $timeTo = $request->hour_to.':'.$request->min_to.' '.$request->meridian_to;
        $data['timestamp_from'] = strtotime($timeFrom);
        $data['timestamp_to'] = strtotime($timeTo);
        $data['full'] = $timeFrom.' - '.$timeTo;

        if( $timeFrom == $timeTo ) {
            return back()->with('flash_danger', __('msg.invalid_time_slot'));
        }

        $this->timeSlotRepo->update($ts_id, $data);
        return redirect()->route('ttr.manage', $request->ttr_id)->with('flash_success', __('msg.update_ok'));
    }

    public function deleteTimeSlot($ts_id)
    {
        $this->timeSlotRepo->delete($ts_id);
        return back()->with('flash_success', __('msg.delete_ok'));
    }


    /*********** RECORDS *************/

    public function editRecord($ttr_id)
    {
        $data['ttr'] = $timeTableRecord = $this->timeTableRecordRepo->find($ttr_id);
        $data['exams'] = $this->examRepo->getExam([ 'year' => $timeTableRecord->year ]);
        $data['my_courses'] = $this->myCourseRepo->getAll();

        return view('pages.support_team.timetables.edit', $data);
    }

    public function showRecord($ttr_id)
    {
        $dayTime = [];
        $data['ttr'] = $timeTableRecord = $this->timeTableRecordRepo->find($ttr_id);
        $data['ttr_id'] = $ttr_id;
        $data['my_course'] = $this->myCourseRepo->find($timeTableRecord->my_course_id);
        $data['time_slots'] = $timeSlots = $this->timeTableRepo->getTimeSlotByTTR($ttr_id);
        $data['tts'] = $timeTables = $this->timeTableRepo->getTimeTable([ 'ttr_id' => $ttr_id ]);

        if( $timeTableRecord->exam_id ) {
            $data['exam_id'] = $timeTableRecord->exam_id;
            $data['exam'] = $this->examRepo->find($timeTableRecord->exam_id);
            $data['days'] = $days = $timeTables->unique('exam_date')->pluck('exam_date');
            $examDate = 'exam_date';
        } else {
            $data['days'] = $days = $timeTables->unique('day')->pluck('day');
            $examDate = 'day';
        }

        foreach( $days as $day ) {
            foreach( $timeSlots as $timeSlot ) {
                $dayTime[] = [
                    'day' => $day,
                    'time' => $timeSlot->full,
                    'subject' => $timeTables->where('ts_id', $timeSlot->id)->where($examDate, $day)->first()->subject->name
                        ?? NULL
                ];
            }
        }
        $data['d_time'] = collect($dayTime);

        return view('pages.support_team.timetables.show', $data);
    }

    public function printRecord($ttr_id)
    {
        $dayTime = [];
        $data['ttr'] = $timeTableRecord = $this->timeTableRecordRepo->find($ttr_id);
        $data['ttr_id'] = $ttr_id;
        $data['my_course'] = $this->myCourseRepo->find($timeTableRecord->my_course_id);

        $data['time_slots'] = $timeSlots = $this->timeTableRepo->getTimeSlotByTTR($ttr_id);
        $data['tts'] = $timeTables = $this->timeTableRepo->getTimeTable([ 'ttr_id' => $ttr_id ]);

        if( $timeTableRecord->exam_id ) {
            $data['exam_id'] = $timeTableRecord->exam_id;
            $data['exam'] = $this->examRepo->find($timeTableRecord->exam_id);
            $data['days'] = $days = $timeTables->unique('exam_date')->pluck('exam_date');
            $examDate = 'exam_date';
        } else {
            $data['days'] = $days = $timeTables->unique('day')->pluck('day');
            $examDate = 'day';
        }

        foreach( $days as $day ) {
            foreach( $timeSlots as $timeSlot ) {
                $dayTime[] = [
                    'day' => $day,
                    'time' => $timeSlot->full,
                    'subject' => $timeTables->where('ts_id', $timeSlot->id)->where($examDate, $day)->first()->subject->name
                        ?? NULL
                ];
            }
        }

        $data['d_time'] = collect($dayTime);
        $data['s'] = Setting::all()->flatMap(function ($s) {
            return [ $s->type => $s->description ];
        });

        return view('pages.support_team.timetables.print', $data);
    }

    public function storeRecord(TTRecordRequest $request)
    {
        $data = $request->validated();
        $data['year'] = $this->year;
        $this->timeTableRecordRepo->create($data);

        return JsonHelper::jsonStoreSuccess();
    }

    public function updateRecord(TTRecordRequest $request, $id)
    {
        $data = $request->validated();
        $this->timeTableRecordRepo->update($id, $data);

        return JsonHelper::jsonUpdateSuccess();
    }

    public function deleteRecord($ttr_id)
    {
        $this->timeTableRecordRepo->delete($ttr_id);
        return back()->with('flash_success', __('msg.delete_ok'));
    }
}

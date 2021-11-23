<?php

namespace App\Helpers;

use App\Models\Exam;
use App\Models\ExamRecord;
use App\Models\Mark;

class CheckExamInfoHelper
{
    public static function examIsLocked()
    {
        return GetSystemInfoHelper::getSetting('lock_exam');
    }

    /*Get Exam Avg Per Term*/
//    public static function getTermAverage($student_id, $term, $year)
//    {
//        $exam = self::getExamByTerm($term, $year);
//        $data = [ 'exam_id' => $exam->id, 'student_id' => $student_id, 'year' => $year ];
//
//        if( $term < 3 ) {
//            $examRecord = ExamRecord::where($data);
//            $average = $examRecord->first()->ave ?: NULL;
//            return $average > 0 ? round($average, 1) : $average;
//        }
//
//        $mark = Mark::where($data)->whereNotNull('tex3');
//        $average = $mark->select('tex3')->avg('tex3');
//        return round($average, 1);
//    }

    public static function getExamByTerm($term, $year)
    {
        $data = [ 'term' => $term, 'year' => $year ];
        return Exam::where($data)->first();
    }

//    public static function getTermTotal($student_id, $term, $year)
//    {
//        $exam = self::getExamByTerm($term, $year);
//        $data = [ 'exam_id' => $exam->id, 'student_id' => $student_id, 'year' => $year ];
//
//        if( $term < 3 ) {
//            return ExamRecord::where($data)->first()->total ?? NULL;
//        }
//
//        $mark = Mark::where($data)->whereNotNull('tex3');
//        return $mark->select('tex3')->sum('tex3');
//    }


}

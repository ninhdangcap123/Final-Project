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
    public static function getTermAverage($st_id, $term, $year)
    {
        $exam = self::getExamByTerm($term, $year);
        $d = [ 'exam_id' => $exam->id, 'student_id' => $st_id, 'year' => $year ];

        if( $term < 3 ) {
            $exr = ExamRecord::where($d);
            $avg = $exr->first()->ave ?: NULL;
            return $avg > 0 ? round($avg, 1) : $avg;
        }

        $mk = Mark::where($d)->whereNotNull('tex3');
        $avg = $mk->select('tex3')->avg('tex3');
        return round($avg, 1);
    }

    public static function getExamByTerm($term, $year)
    {
        $d = [ 'term' => $term, 'year' => $year ];
        return Exam::where($d)->first();
    }

    public static function getTermTotal($st_id, $term, $year)
    {
        $exam = self::getExamByTerm($term, $year);
        $d = [ 'exam_id' => $exam->id, 'student_id' => $st_id, 'year' => $year ];

        if( $term < 3 ) {
            return ExamRecord::where($d)->first()->total ?? NULL;
        }

        $mk = Mark::where($d)->whereNotNull('tex3');
        return $mk->select('tex3')->sum('tex3');
    }


}

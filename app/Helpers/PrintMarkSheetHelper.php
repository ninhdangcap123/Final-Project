<?php

namespace App\Helpers;

use App\Models\ExamRecord;
use App\Models\Grade;
use App\Models\Mark;
use Illuminate\Database\Eloquent\Collection;

class PrintMarkSheetHelper
{
    public static function getRemarks()
    {
        return [ 'Average', 'Credit', 'Distinction', 'Excellent', 'Fail', 'Fair', 'Good', 'Pass', 'Poor', 'Very Good', 'Very Poor' ];
    }

    public static function getSuffix($number)
    {
        if( $number < 1 ) {
            return NULL;
        }

        $ends = array( 'th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th' );
        if( ( ( $number % 100 ) >= 11 ) && ( ( $number % 100 ) <= 13 ) )
            return $number.'<sup>th</sup>';
        else
            return $number.'<sup>'.$ends[$number % 10].'</sup>';
    }

    public static function getSubTotalTerm($st_id, $sub_id, $term, $my_course_id, $year)
    {
        $d = [ 'student_id' => $st_id, 'subject_id' => $sub_id, 'my_course_id' => $my_course_id, 'year' => $year ];

        $tex = 'tex'.$term;
        $sub_total = Mark::where($d)->select($tex)->get()->where($tex, '>', 0);
        return $sub_total->count() > 0 ? $sub_total->first()->$tex : '-';
    }

    public static function getGradeList($major_id)
    {
        $grades = Grade::where([ 'major_id' => $major_id ])->orderBy('name')->get();

        if( $grades->count() < 1 ) {
            $grades = Grade::whereNull('major_id')->orderBy('name')->get();
        }
        return $grades;
    }

    public static function deleteOldRecord($st_id, $my_course_id)
    {
        $d = [ 'student_id' => $st_id, 'year' => self::getCurrentSession() ];

        $marks = Mark::where('my_course_id', '<>', $my_course_id)->where($d);
        if( $marks->get()->count() > 0 ) {
            $exr = ExamRecord::where('my_course_id', '<>', $my_course_id)->where($d);
            $marks->delete();
            $exr->delete();
        }
        return true;
    }

    public static function countDistinctions(Collection $marks)
    {
        $gradeIDS = Grade::where('name', 'LIKE', 'A%')->orWhere('name', 'LIKE', 'B%')->get()->pluck('id')->toArray();
        return self::markGradeFilter($marks, $gradeIDS);
    }

    protected static function markGradeFilter(Collection $marks, $gradeIDS)
    {
        return $marks->filter(function ($mks) use ($gradeIDS) {
            return in_array($mks->grade_id, $gradeIDS);
        })->count();
    }

    public static function countPasses(Collection $marks)
    {
        $gradeIDS = Grade::where('name', 'LIKE', 'D%')->orWhere('name', 'LIKE', 'E%')->get()->pluck('id')->toArray();
        return self::markGradeFilter($marks, $gradeIDS);
    }

    public static function countCredits(Collection $marks)
    {
        $gradeIDS = Grade::where('name', 'LIKE', 'C%')->get()->pluck('id')->toArray();
        return self::markGradeFilter($marks, $gradeIDS);
    }

    public static function countFailures(Collection $marks)
    {
        $gradeIDS = Grade::where('name', 'LIKE', 'F%')->get()->pluck('id')->toArray();
        return self::markGradeFilter($marks, $gradeIDS);
    }

    public static function countStudents($exam_id, $my_course_id, $class_id, $year)
    {
        $d = [ 'exam_id' => $exam_id, 'my_course_id' => $my_course_id, 'class_id' => $class_id, 'year' => $year ];
        return Mark::where($d)->select('student_id')->distinct()->get()->count();
    }

    public static function countSubjectsOffered(Collection $mark)
    {
        return $mark->filter(function ($mk) {
            return ( $mk->tca + $mk->exm ) > 0;

        })->count();
    }


}

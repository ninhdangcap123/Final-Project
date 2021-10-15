<?php

namespace App\Helpers;

use App\Models\StudentRecord;
use Illuminate\Support\Facades\Auth;

class checkUsersHelper
{
    public static function userIsTeamSAS(){
        return in_array(Auth::user()->user_type, getUsersHelper::getTeamSAS());
    }

    public static function userIsTeamSAT()
    {
        return in_array(Auth::user()->user_type, getUsersHelper::getTeamSAT());
    }
    public static function userIsTeamSA()
    {
        return in_array(Auth::user()->user_type, getUsersHelper::getTeamSA());
    }

    public static function userIsTeamAccount()
    {
        return in_array(Auth::user()->user_type, getUsersHelper::getTeamAccount());
    }
    public static function userIsAcademic()
    {
        return in_array(Auth::user()->user_type, getUsersHelper::getTeamAcademic());
    }

    public static function userIsAdministrative()
    {
        return in_array(Auth::user()->user_type, getUsersHelper::getTeamAdministrative());
    }
    public static function userIsMyChild($student_id, $parent_id)
    {
        $data = ['user_id' => $student_id, 'my_parent_id' =>$parent_id];
        return StudentRecord::where($data)->exists();
    }
    public static function userIsPTA()
    {
        return in_array(Auth::user()->user_type, getUsersHelper::getPTA());
    }

    public static function headSA(int $user_id)
    {
        return $user_id === 1;
    }

}

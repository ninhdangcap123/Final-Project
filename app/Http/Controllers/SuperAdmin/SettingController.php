<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helpers\GetPathHelper;
use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Requests\SettingUpdate;
use App\Repositories\MyCourseRepo;
use App\Repositories\SettingRepo;

class SettingController extends Controller
{
    protected $setting, $my_course;

    public function __construct(SettingRepo $setting, MyCourseRepo $my_course)
    {
        $this->setting = $setting;
        $this->my_course = $my_course;
    }

    public function index()
    {
         $s = $this->setting->all();
         $d['majors'] = $this->my_course->getMajor();
         $d['s'] = $s->flatMap(function($s){
            return [$s->type => $s->description];
        });
        return view('pages.super_admin.settings', $d);
    }

    public function update(SettingUpdate $req)
    {
        $sets = $req->except('_token', '_method', 'logo');
        $sets['lock_exam'] = $sets['lock_exam'] == 1 ? 1 : 0;
        $keys = array_keys($sets);
        $values = array_values($sets);
        for($i=0; $i<count($sets); $i++){
            $this->setting->update($keys[$i], $values[$i]);
        }

        if($req->hasFile('logo')) {
            $logo = $req->file('logo');
            $f = GetPathHelper::getFileMetaData($logo);
            $f['name'] = 'logo.' . $f['ext'];
            $f['path'] = $logo->storeAs(GetPathHelper::getPublicUploadPath(), $f['name']);
            $logo_path = asset('storage/' . $f['path']);
            $this->setting->update('logo', $logo_path);
        }

        return back()->with('flash_success', __('msg.update_ok'));

    }
}

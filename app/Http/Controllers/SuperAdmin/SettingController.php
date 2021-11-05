<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helpers\GetPathHelper;
use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Requests\SettingUpdate;
use App\Repositories\Major\MajorRepositoryInterface;
use App\Repositories\MyCourseRepo;
use App\Repositories\Setting\SettingRepository;
use App\Repositories\Setting\SettingRepositoryInterface;
use App\Repositories\SettingRepo;

class SettingController extends Controller
{
    protected $setting, $major;

    public function __construct(SettingRepositoryInterface $setting, MajorRepositoryInterface $major)
    {
        $this->setting = $setting;

        $this->major = $major;
    }

    public function index()
    {
         $s = $this->setting->getAll();
         $d['majors'] = $this->major->getAll();
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
            $file = GetPathHelper::getFileMetaData($logo);
            $file['name'] = 'logo.' . $file['ext'];
            $file['path'] = $logo->storeAs(GetPathHelper::getPublicUploadPath(), $file['name']);
            $logo_path = asset('storage/' . $file['path']);
            $this->setting->update('logo', $logo_path);

        }

        return back()->with('flash_success', __('msg.update_ok'));

    }
}

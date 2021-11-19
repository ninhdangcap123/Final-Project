<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helpers\GetPathHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SettingUpdate;
use App\Repositories\Major\MajorRepositoryInterface;

use App\Repositories\Setting\SettingRepositoryInterface;
use Illuminate\Http\RedirectResponse;


class SettingController extends Controller
{
    protected $settingRepo;
    protected $majorRepo;

    public function __construct(
        SettingRepositoryInterface $settingRepo,
        MajorRepositoryInterface   $majorRepo)
    {
        $this->settingRepo = $settingRepo;
        $this->majorRepo = $majorRepo;
    }

    public function index()
    {
        $setting = $this->settingRepo->getAll();
        $data['majors'] = $this->majorRepo->getAll();
        $data['s'] = $setting->flatMap(function ($s) {
            return [ $s->type => $s->description ];
        });
        return view('pages.super_admin.settings', $data);
    }

    public function update(SettingUpdate $request): RedirectResponse
    {
        $settings = $request->except('_token', '_method', 'logo');
        $settings['lock_exam'] = $settings['lock_exam'] == 1 ? 1 : 0;
        $keys = array_keys($settings);
        $values = array_values($settings);
        for( $i = 0; $i < count($settings); $i++ ) {
            $this->settingRepo->update($keys[$i], $values[$i]);
        }

        if( $request->hasFile('logo') ) {
            $logo = $request->file('logo');
            $file = GetPathHelper::getFileMetaData($logo);
            $file['name'] = 'logo.'.$file['ext'];
            $file['path'] = $logo->storeAs(GetPathHelper::getPublicUploadPath(), $file['name']);
            $logo_path = asset('storage/'.$file['path']);
            $this->settingRepo->update('logo', $logo_path);

        }

        return back()->with('flash_success', __('msg.update_ok'));

    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\CheckUsersHelper;
use App\Helpers\GetSystemInfoHelper;
use App\Helpers\Qs;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRepo;

class HomeController extends Controller
{
    protected $user;
    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }


    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function privacyPolicy()
    {
        $data['app_name'] = config('app.name');
        $data['app_url'] = config('app.url');
        $data['contact_phone'] = GetSystemInfoHelper::getSetting('phone');
        return view('pages.other.privacy_policy', $data);
    }

    public function termsOfUse()
    {
        $data['app_name'] = config('app.name');
        $data['app_url'] = config('app.url');
        $data['contact_phone'] = GetSystemInfoHelper::getSetting('phone');
        return view('pages.other.terms_of_use', $data);
    }

    public function dashboard()
    {
        $d=[];
        if(CheckUsersHelper::userIsTeamSAT()){
            $d['users'] = $this->user->getAll();
        }

        return view('pages.support_team.dashboard', $d);
    }
}

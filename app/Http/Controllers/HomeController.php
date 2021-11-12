<?php

namespace App\Http\Controllers;

use App\Helpers\CheckUsersHelper;
use App\Helpers\GetSystemInfoHelper;
use App\Helpers\Qs;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRepo;

class HomeController extends Controller
{
    protected $userRepo;
    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
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
        $data=[];
        if(CheckUsersHelper::userIsTeamSAT()){
            $data['users'] = $this->userRepo->getAll();
        }

        return view('pages.support_team.dashboard', $data);
    }
}

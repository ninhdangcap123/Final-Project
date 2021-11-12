<?php

namespace App\Http\Controllers;


use App\Helpers\GetPathHelper;
use App\Http\Requests\UserChangePass;
use App\Http\Requests\UserUpdate;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MyAccountController extends Controller
{
    protected $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function editProfile()
    {
        $data['my'] = Auth::user();
        return view('pages.support_team.my_account', $data);
    }

    public function updateProfile(UserUpdate $request)
    {
        $user = Auth::user();

        $data = $user->username ? $request->only([ 'email', 'phone', 'address' ]) : $request->only([ 'email', 'phone', 'address', 'username' ]);

        if( !$user->username && !$request->username && !$request->email ) {
            return back()->with('pop_error', __('msg.user_invalid'));
        }

        $userType = $user->user_type;
        $code = $user->code;

        if( $request->hasFile('photo') ) {
            $photo = $request->file('photo');
            $file = GetPathHelper::getFileMetaData($photo);
            $file['name'] = 'photo.'.$file['ext'];
            $file['path'] = $photo->storeAs(GetPathHelper::getUploadPath($userType).$code, $file['name']);
            $data['photo'] = asset('storage/'.$file['path']);
        }

        $this->userRepo->update($user->id, $data);
        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function changePass(UserChangePass $request)
    {
        $userId = Auth::user()->id;
        $myPassword = Auth::user()->password;
        $oldPassword = $request->current_password;
        $newPassword = $request->password;

        if( password_verify($oldPassword, $myPassword) ) {
            $data['password'] = Hash::make($newPassword);
            $this->userRepo->update($userId, $data);
            return back()->with('flash_success', __('msg.p_reset'));
        }

        return back()->with('flash_danger', __('msg.p_reset_fail'));
    }

}

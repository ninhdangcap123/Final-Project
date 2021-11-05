<?php

namespace App\Http\Controllers;


use App\Helpers\GetPathHelper;
use App\Helpers\Qs;
use App\Http\Requests\UserChangePass;
use App\Http\Requests\UserUpdate;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MyAccountController extends Controller
{
    protected $user;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }

    public function editProfile()
    {
        $data['my'] = Auth::user();
        return view('pages.support_team.my_account', $data);
    }

    public function updateProfile(UserUpdate $req)
    {
        $user = Auth::user();

        $d = $user->username ? $req->only(['email', 'phone', 'address']) : $req->only(['email', 'phone', 'address', 'username']);

        if(!$user->username && !$req->username && !$req->email){
            return back()->with('pop_error', __('msg.user_invalid'));
        }

        $user_type = $user->user_type;
        $code = $user->code;

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = GetPathHelper::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(GetPathHelper::getUploadPath($user_type).$code, $f['name']);
            $d['photo'] = asset('storage/' . $f['path']);
        }

        $this->user->update($user->id, $d);
        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function changePass(UserChangePass $req)
    {
        $user_id = Auth::user()->id;
        $my_pass = Auth::user()->password;
        $old_pass = $req->current_password;
        $new_pass = $req->password;

        if(password_verify($old_pass, $my_pass)){
            $data['password'] = Hash::make($new_pass);
            $this->user->update($user_id, $data);
            return back()->with('flash_success', __('msg.p_reset'));
        }

        return back()->with('flash_danger', __('msg.p_reset_fail'));
    }

}

<?php

namespace App\Http\Controllers\admin;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function showChangePassword(){
        return view('admin.change-password');
    }

    public function updatePassword(Request $request){
        $validator = Validator::make($request->all(), [
            'old_password' =>'required',
            'new_password' =>'required|min:8',
            'confirm_password' => 'required|same:new_password'
        ]);

        $validator->after(function ($validator) use ($request) {
            if (Hash::check($request->new_password, $request->user()->password)) {
                $validator->errors()->add('new_password', 'The new password must be different from the old password.');
            }
        });

        $admin = User::where('id',Auth::guard('admin')->user()->id)->first();

        if ($validator->passes()) {


            if (!Hash::check($request->old_password, $admin->password)) {

                session()->flash('not-found','Old password is incorrect');
                return response()->json([
                   'status' => true,
                   'message'=> 'Old password is incorrect'
                ]);

            }

            User::where('id',Auth::guard('admin')->user()->id)->update([
                'password' => Hash::make($request->new_password)
            ]);

            session()->flash('create-success','you have changed your password successfully');

            return response()->json([
                'status' => true,
                'message'=> 'you have changed your password successfully'
            ]);

        } else{
            return response()->json([
                'status' => false,
                'errors'=> $validator->errors()
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminHomeController extends Controller
{
    public function index(){
        return view('admin.dashboard');
        // $admin = Auth::guard('admin')->user();
        // echo"home".$admin->name. '<a href="'.route('admin.logout').'">Logout</a>';
    }
    public function logout(Request $request){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}

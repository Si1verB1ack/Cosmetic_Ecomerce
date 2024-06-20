<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItems;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(){
        return view('front.account.login');
    }

    public function register(){
        return view('front.account.register');
    }

    public function processRegister(Request $request){

        $validator = Validator::make($request->all(), [
            'name'=> 'required|min:3',
            'email'=> 'required|email|unique:users',
            'password'=> 'required|min:8|confirmed',
        ]) ;

        if ($validator->passes()) {

            // create a new user account
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('login-success','You have been registered successfully');

            return response()->json([
               'status' => true,
               'message'=> 'You have been registered successfully'
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors'=> $validator->errors()
            ]);
        }
    }
    // create authenticated function for user account
    public function authenticate(Request $request){
        $validator = Validator::make($request->all(), [
            'email'=> 'required|email',
            'password'=> 'required|min:8',
        ]) ;
        if($validator->passes()){

            if(Auth::attempt(['email'=>$request->email,'password'=>$request->password],$request->get('remember'))){

                if(session()->has('url.intended')){
                    return redirect(session()->get('url.intended'));
                }

                session()->flash('login-success','Login successfully\nWelcome '.Auth::user()->name);

                return redirect()->route('account.profile');

            }else{
                session()->flash('login-failed','Either email or password is incorrect');
                return redirect()->route('account.login')->withInput($request->only('email'));
            }

        }else{
            session()->flash('login-failed','Login failed');
                return redirect()->route('account.login')->withInput($request->only('email'));
        }
    }

    //create profile function
    public function profile(){
        $user = Auth::user();
        return view('front.account.profile',compact('user'));
    }

    //logout profile function
    public function logout(){
        Auth::logout();
        session()->flash('login-success','You have been logout successfully');
        return redirect()->route('account.login');
    }
    public function orders(){

        $user = Auth::user();

        $orders = Order::where('user_id',$user->id)->orderBy('created_at','DESC')->get();

        $data['orders'] = $orders;

        return view('front.account.order',$data);
    }

    public function OrderDetail($id){
        $user = Auth::user();

        $order = Order::where('user_id',$user->id)->where('id',$id)->first();
        $orderItems = OrderItems::where('order_id',$id)->get();

        $data['order'] = $order;
        $data['orderItems'] = $orderItems;

        return view('front.account.order-detail',$data);
    }
}

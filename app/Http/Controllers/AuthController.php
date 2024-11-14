<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\User;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
                    $intendedUrl = session()->get('url.intended');

                    if ($intendedUrl && Str::contains($intendedUrl, 'admin')) {
                        session()->forget('url.intended');
                        session()->flash('login-success', 'Login successfully\nWelcome ' . Auth::user()->name);
                        return redirect()->route('account.profile');
                    }
                    session()->flash('login-success','Login successfully\nWelcome '.Auth::user()->name);
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

        $userId = Auth::user()->id;
        $countries = Country::OrderBy('name','ASC')->get();
        $user = User::where('id',Auth::user()->id)->first();
        $address = CustomerAddress::where('user_id',$userId)->first();

        return view('front.account.profile',[
            'user' => $user,
            'countries' => $countries,
            'address' => $address,
        ]);
    }

    public function updateProfile(Request $request){
        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'name'=> 'required|min:3',
            'email'=> 'required|email|unique:users,email,'.$userId.',id',
            'phone'=> 'required',
        ]) ;

        if ($validator->passes()) {

            $user = User::find($userId);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();

            session()->flash('create-success','Profile updated successfully');

            return response()->json([
               'status' => true,
               'message'=> 'Profile updated successfully'
            ]);

        }else{
            return response()->json([
               'status' => false,
                'errors'=> $validator->errors()
            ]);
        }
    }
    public function updateAddress(Request $request){
        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(),[
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'apartment' => 'required',
            'address' => 'required|min:20',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);

        if ($validator->passes()){
            // session()->flash('delete-success','Category created successfully');
            CustomerAddress::updateOrCreate(
                ['user_id' => $userId],
                [
                    'user_id' => $userId,
                    'first_name' =>  $request->first_name,
                    'last_name' =>  $request->last_name,
                    'email' =>  $request->email,
                    'mobile' =>  $request-> mobile,
                    'country_id' =>  $request-> country,
                    'address' =>  $request->address,
                    'apartment' =>  $request->apartment,
                    'city' =>  $request->city,
                    'state' =>  $request-> state,
                    'zip' =>  $request-> zip,

                ]
            );

            session()->flash('create-success','Profile updated successfully');

            return response()->json([
               'status' => true,
               'message'=> 'Profile updated successfully'
            ]);

        }else{
            return response()->json([
                'message'=> 'please fix the error message',
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }


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
        $orderItemsCount = OrderItems::where('order_id',$id)->get()->count();

        $data['order'] = $order;
        $data['orderItems'] = $orderItems;
        $data['orderItemsCount'] = $orderItemsCount;

        return view('front.account.order-detail',$data);
    }
    public function wishlist(){
        $wishlists = Wishlist::where('user_id',Auth::user()->id)->with('product')->get();
        $data['wishlists'] = $wishlists;

        return view('front.account.wishlist',$data);
    }
    public function removeProductFromWishlist(Request $request){
        $wishlist = Wishlist::where('user_id',Auth::user()->id)->where('product_id',$request->id)->first();
        if($wishlist==null){
            session()->flash('not-found','product already removed');
            return response()->json([
               'status'=>true,
               'message'=>'product already removed'
            ]);
        }else{
            $wishlist = Wishlist::where('user_id',Auth::user()->id)->where('product_id',$request->id)->delete();
            session()->flash('create-success','product removed successfully');
            return response()->json([
               'status'=>true,
               'message'=>'product already removed'
            ]);
        }
    }

    public function showChangePassword(){
        return view('front.account.change-password');
    }

    public function updatePassword(Request $request){
        $validator = Validator::make($request->all(), [
            'old_password' =>'required',
            'new_password' =>'required|min:8',
            'confirm_password' => 'required|same:new_password'
        ]) ;

        if ($validator->passes()) {

            $user = User::select('id','password')->where('id',Auth::user()->id)->first();

            if (!Hash::check($request->old_password, $user->password)) {

                session()->flash('not-found','Old password is incorrect');
                return response()->json([
                   'status' => true,
                   'message'=> 'Old password is incorrect'
                ]);

            }

            User::where('id',$user->id)->update([
                'password' => Hash::make($request->new_password)
            ]);

            session()->flash('create-success','Password updated successfully');
            return response()->json([
                'status' => true,
                'message'=> 'Password updated successfully'
            ]);

        } else{
            return response()->json([
                'status' => false,
                'errors'=> $validator->errors()
            ]);
        }
    }

    public function forgetPassword(){
        return view('front.account.forget-password');
    }

    public function processForgetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' =>'required|exists:users,email',
        ]) ;

        if ($validator->fails()) {
            return redirect()->route('front.forgetPassword')->withInput()->withErrors($validator);
        }

        $token = Str::random(60);
        Carbon::setLocale('Asia/Phnom_Penh');

        DB::table('password_reset_tokens')->where('email',$request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now('Asia/Phnom_Penh'),
        ]);

        $user = User::where('email', $request->email)->first();

        $formData = [
            'token'=>$token,
            'user' => $user,
            'mailSubject' => 'You have requested to reset your password'
        ];

        Mail::to($request->email)->send(new ResetPassword($formData));

        return redirect()->route('front.forgetPassword')->with('success','Please check your inbox to reset your password');
    }

    public function resetPassword($token){

        $tokenExist = DB::table('password_reset_tokens')->where('token',$token)->first();

        if($tokenExist == null){
            return redirect()->route('front.forgetPassword')->with('error','Invalid Request');
        }

        return view('front.account.reset-password',[
            'token' => $token,
        ]);
    }

    public function processResetPassword(Request $request){

        $token = $request->token;

        $tokenObj = DB::table('password_reset_tokens')->where('token',$token)->first();

        if($tokenObj == null){
            return redirect()->route('front.forgetPassword')->with('error','Invalid Request');
        }

        $user = User::where('email', $tokenObj->email)->first();

        $validator = Validator::make($request->all(), [
            'new_password'=> 'required|min:8',
            'confirm_password'=> 'required|same:new_password',
        ]) ;

        if ($validator->fails()) {
            return redirect()->route('front.resetPassword',$token)->withInput()->withErrors($validator);
        }

        User::where('id', $user->id)->update([
            'password'=> Hash::make($request->new_password)
        ]);

        return redirect()->route('account.login')->with('success','Your password has been reset successfully');
    }

}

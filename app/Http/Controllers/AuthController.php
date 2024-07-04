<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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
}

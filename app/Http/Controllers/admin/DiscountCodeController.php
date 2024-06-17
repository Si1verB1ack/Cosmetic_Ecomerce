<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class DiscountCodeController extends Controller
{
    //create the 5 functions
    public function index(){
        return view('admin.coupon.list');
    }
    public function create(){
        return view('admin.coupon.create');
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required'
        ]);
        if ($validator->passes()) {
            //check to make sure that the starting date is greater than the current date
            if(!empty($request->starts_at)){
                $now = Carbon::now();
                $startAt=Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);
                if($startAt->lte($now)==true) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => 'Start date must be greater than the current date time']
                    ]);
                }
            }
            //check to make sure that the starting date is greater than the current date
            if(!empty(!empty($request->starts_at) && !empty($request->expires_at))){
                $now = Carbon::now();

                $startAt=Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);
                $expireAt=Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);

                if($expireAt->gt($startAt)==false) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expires date must be greater than the start date']
                    ]);
                }
            }


            $discountCode = new DiscountCoupon();
            $discountCode->name = $request->name;
            $discountCode->code = $request->code;
            $discountCode->description = $request->description;
            $discountCode->max_uses = $request->max_uses;
            $discountCode->max_uses_user = $request->max_uses_user;
            $discountCode->type = $request->type;
            $discountCode->discount_amount = $request->discount_amount;
            $discountCode->status = $request->status;
            $discountCode->min_amount = $request->min_amount;
            $discountCode->starts_at = $request->starts_at;
            $discountCode->expires_at = $request->expires_at;
            $discountCode->save();


            $message = 'Discount coupon added successfully';

            session()->flash("add-success",$message);
            return response()->json([
                'status' => true,
                'message'=> $message,
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function edit(){

    }
    public function update(){

    }
    public function delete(){

    }
}

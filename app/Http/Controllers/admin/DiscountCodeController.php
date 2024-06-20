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
    public function index(Request $request){
        $discountCoupons = DiscountCoupon::latest('id');
        // dd($discountCoupons = Category::latest());
        if(!empty($request->get('keyword'))){
            $discountCoupons = $discountCoupons->where('name','like','%'. $request->get('keyword'). '%');
            $discountCoupons = $discountCoupons->orWhere('code','like','%'. $request->get('keyword'). '%');
        }
        $discountCoupons = $discountCoupons->paginate(10);

        return view('admin.coupon.list',compact('discountCoupons'));
    }
    public function create(){
        return view('admin.coupon.create');
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
            'starts_at' => 'required',
            'expires_at' => 'required'

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

            session()->flash("create-success",$message);
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
    public function edit(Request $request, $id){
        $coupon = DiscountCoupon::find($id);
        // dd($coupon = Category::latest());
        if(empty($coupon)){
            session()->flash("not-found","Record not found");
            // return response()->json([
            //     'status' => false,
            //     'message'=> 'Record not found'
            // ]);
            // return redirect()->route('categories.index');
            return redirect()->route('coupons.index')->with('not-found','Record not found');
        }
        // dd($category);
        // return view('admin.category.edit',compact('category'));

        return view('admin.coupon.edit',compact('coupon'));
    }
    public function update(Request $request, $id){
        $coupon = DiscountCoupon::find($id);

        if(empty($coupon)){
            // return redirect()->route('categories.index');
            session()->flash("not-found","Record not found");
            return response()->json([
                'status' => false,
                'message'=> 'Record not found'
            ]);
        }

        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
            'starts_at' => 'required',
            'expires_at' => 'required'
        ]);
        if ($validator->passes()) {
            //check to make sure that the starting date is greater than the current date

            //we do not do it here because when we want to update it the current date changes

            // if(!empty($request->starts_at)){
            //     $now = Carbon::now();
            //     $startAt=Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);
            //     if($startAt->lte($now)==true) {
            //         return response()->json([
            //             'status' => false,
            //             'errors' => ['starts_at' => 'Start date must be greater than the current date time']
            //         ]);
            //     }
            // }
            //check to make sure that the starting date is greater than the current date
            // $startAt=Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);
            if(!empty(!empty($request->starts_at) && !empty($request->expires_at))){
                // $now = Carbon::now();
                $startAt=Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);
                $expireAt=Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);

                if($expireAt->gt($startAt)==false) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expires date must be greater than the start date']
                    ]);
                }
            }

            $coupon->name = $request->name;
            $coupon->code = $request->code;
            $coupon->description = $request->description;
            $coupon->max_uses = $request->max_uses;
            $coupon->max_uses_user = $request->max_uses_user;
            $coupon->type = $request->type;
            $coupon->discount_amount = $request->discount_amount;
            $coupon->status = $request->status;
            $coupon->min_amount = $request->min_amount;
            $coupon->starts_at = $request->starts_at;
            $coupon->expires_at = $request->expires_at;
            $coupon->save();


            $message = 'Discount coupon updated successfully';

            session()->flash("create-success",$message);
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
    public function destroy(Request $request, $id){
        $coupon = DiscountCoupon::find($id);
        // dd($coupon = Category::latest());
        if(empty($coupon)){
            // return redirect()->route('categories.index');
            session()->flash("not-found","Record not found");
            return response()->json([
                'status' => false,
                'message'=> 'Record not found'
            ]);
        }

        $coupon->delete();

        session()->flash('delete-success','Coupon delete successfully');

        return response()->json([
            'status' => true,
            'message'=> 'Coupon delete successfully'
        ]);

    }
}

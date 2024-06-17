<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function create(){
        $countries = Country::get();
        $data['countries'] = $countries;

        $shippingCharges = ShippingCharge::select('shipping_charges.*','countries.name')
                                            ->leftJoin('countries','countries.id','shipping_charges.country_id')->get();
        $data['shippingCharges'] = $shippingCharges;

        return view('admin.shipping.create',$data);
    }
    public function store(Request $request){

        $count = ShippingCharge::where('country_id',$request->country)->count();

        $validator = Validator::make(request()->all(), [
            'country'=> 'required',
            'amount'=> 'required|numeric',
        ]) ;
        if ($validator->passes()) {

            if($count>0) {

                session()->flash("not-found","Shipping Already exists");

                return response()->json([
                    'status' => true,
                ]);

            }

            $shipping = new ShippingCharge();
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            session()->flash('create-success','Shipping added successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Category added successfully'
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors'=> $validator->errors()
            ]);
        }

    }
    public function edit($shippingId, Request $request){
        $shippingCharge = ShippingCharge::find($shippingId);

        // if(empty($shipping)){
        //     return redirect()->route('shipping.index')->with("not-found","Record not found");
        // }

        $countries = Country::get();
        $data['countries'] = $countries;
        $data['shippingCharge'] = $shippingCharge;

        return view('admin.shipping.edit',$data);
    }

    public function update($id,Request $request){

        $validator = Validator::make(request()->all(), [
            'country'=> 'required',
            'amount'=> 'required|numeric',
        ]) ;
        if ($validator->passes()) {

            $shipping = ShippingCharge::find($id);
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            session()->flash('create-success','Shipping updated successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Category updated successfully'
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors'=> $validator->errors()
            ]);
        }

    }
    public function destroy($id){

        $shippingCharge = ShippingCharge::find($id);

        if ($shippingCharge==null){
            session()->flash("not-found","Shipping does not exist");

            return response()->json([
                'status' => true,
            ]);

        }

        $shippingCharge->delete();

        session()->flash('create-success','Shipping deleted successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Category deleted successfully'
        ]);

    }
}

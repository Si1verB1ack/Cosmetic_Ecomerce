<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\DiscountCoupon;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    //add to cart
    public function addToCart(Request $request){
        $product = Product::with('product_images')->find($request->id);

        if($product == null){
            // return redirect()->route('categories.index');
            session()->flash("not-found","Record not found");
            return response()->json([
                'status' => false,
                'message'=> 'Product not found'
            ]);
        }

        if(Cart::count()>0){
            $cartContent = Cart::content();
            $productAlreadyExists = false;

            foreach($cartContent as $item){
                if($item->id == $product->id){
                    $productAlreadyExists = true;
                    break;
                }
            }
            if($productAlreadyExists==false){
                Cart::add($product->id, $product->title, 1, $product->price,
                ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
                $status = true;
                $message = $product->title.' added in your cart successfully';
                session()->flash("create-success",$message);
            }else{
                $status = false;
                $message =$product->title.' already Added in cart';
                session()->flash("not-found",$message);
            }
        }else{
            Cart::add($product->id, $product->title, 1, $product->price,
            ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
            $status = true;
            $message = $product->title.' added in cart successfully';
            session()->flash("add-success",$message);
        }
        // session()->flash('create-success',$message);

        session()->flash("add-success",$message);
        return response()->json([
            'status' => $status,
            'message'=> $message
        ]);
    }
    public function cart()
    {
        $cartContent = Cart::content();
        // dd($cartContent);
        $data['cartContent'] = $cartContent;
        return view('front.cart',$data);
    }

    public function updateCart(Request $request){
        $rowId = $request->rowId;
        $qty = $request->qty;

        $itemInfo = Cart::get($rowId);

        //check qty if available
        $product = Product::find($itemInfo->id);

        if($product->track_qty == 'Yes'){

            if($qty <= $product->qty){

                Cart::update($rowId,$qty);
                $message = 'Cart updated successfully';
                $status = true;
                session()->flash("add-success",$message);

            }else{

                $message = 'Product:'.$product->title.' quantity('.$qty.'). is not available in stock';
                $status = false;
                session()->flash("error",$message);

            }
        }else{

            Cart::update($rowId,$qty);
            $message = 'Cart updated successfully';
            $status = true;
            session()->flash("success",$message);

        }

        return response()->json([
            'status' => $status,
            'message'=> $message,
        ]);

    }
    public function deleteItem(Request $request){
        $rowId = $request->rowId;

        $itemInfo = Cart::get($rowId);

        if($itemInfo==null){
            $message = 'Item not found in cart';
            $status = false;
            session()->flash("error",$message);
            return response()->json([
                'status' => $status,
                'message'=> $message,
            ]);
        }

        Cart::remove($rowId);

        $message = 'Item remove from Cart successfully';
        $status = true;

        session()->flash("add-success",$message);
        return response()->json([
            'status' => $status,
            'message'=> $message,
        ]);

    }

    //create checkout function
    public function checkout(){
        // $cartContent = Cart::content();
        // $data['cartContent'] = $cartContent;
        $discount = 0;

        if(Cart::count()==0){
            return redirect()->route('front.cart');
        }

        if(Auth::check()==false){
            if(!session()->has('url.intended')){
                session(['url.intended' => url()->current()]);
            }
            // return session()->has('url.intended');
            return redirect()->route('account.login');
        }

        $customerAddress = CustomerAddress::where('user_id', Auth::user()->id)->first();

        session()->forget('url.intended');

        $countries = Country::orderBy('name', 'ASC')->get();

        $subTotal = Cart::subtotal(2,'.','');
        //applying an discount
        if(session()->has('code')){
            $code = session()->get('code');

            if($code->type == 'percent'){
                $discount = $subTotal*($code->discount_amount/100);
                // $grandTotal = $subTotal-$discount;
            }else{
                $discount = $code->discount_amount;
            }
        }

        //calculate shipping
        if($customerAddress != null){
            $userCountry = $customerAddress->country_id;

            $shippingInfo = ShippingCharge::where('country_id', $userCountry)->first();

            $totalQty = 0;
            $totalShippingCharge=0;
            $grandTotal=0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            $totalShippingCharge = $totalQty*$shippingInfo->amount;
            $grandTotal=($subTotal-$discount)+$totalShippingCharge;
        }else{
            $grandTotal=($subTotal-$discount);
            $totalShippingCharge =0;
        }

        // dd($customerAddress);

        return view('front.checkout',[
            'countries' => $countries,
            'customerAddress' => $customerAddress,
            'totalShippingCharge' => $totalShippingCharge,
            'discount' => number_format($discount,2),
            'grandTotal' => $grandTotal,
        ]);
    }

    public function processCheckout(Request $request){

        // apply valiation
        $validator = Validator::make($request->all(),[
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required|min:20',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);
        if ($validator->fails()){
            // session()->flash('delete-success','Category created successfully');

            return response()->json([
                'message'=> 'please fix the error message',
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $user = Auth::user();

        //step2 is to save the user addresses
        CustomerAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
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

        //step3 is to store data in orders table

        if($request->payment_method == 'cod'){

            $discountCodeId = NULL;
            $promoCode = '';
            $shipping = 0;
            $discount = 0;
            $subTotal = Cart::subtotal(2,'.','');
            $grandTotal = $subTotal+$shipping;

            //applying an discount
            if(session()->has('code')){
                $code = session()->get('code');

                if($code->type == 'percent'){
                    $discount = $subTotal*($code->discount_amount/100);
                    // $grandTotal = $subTotal-$discount;
                }else{
                    $discount = $code->discount_amount;
                }

                $discountCodeId = $code->id;
                $promoCode = $code->code;
            }

            //calculate shipping
            $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();

            $totalQty = 0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            if($shippingInfo!=null){

                $shipping = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subTotal-$discount)+$shipping;

            }else{

                $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();
                $shipping = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subTotal-$discount)+$shipping;
            }

            $order = new Order;
            $order->subtotal = $subTotal;
            $order->shipping = $shipping;
            $order->discount = $discount;
            $order->grand_total = $grandTotal;
            $order->coupon_code_id = $discountCodeId;
            $order->coupon_code = $promoCode;
            $order->payment_status = 'not paid';
            $order->status = 'pending';
            $order->user_id = $user->id;
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->mobile = $request->mobile;
            $order->address = $request->address;
            $order->apartment = $request->apartment;
            $order->state = $request->state;
            $order->city = $request->city;
            $order->zip = $request->zip;
            $order->notes = $request->notes;
            $order->country_id = $request->country;

            $order->save();


            //step 4 is to store data in order items table
            foreach(Cart::content() as $item){
                $orderItem = new OrderItems;
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->price*$item->qty;
                $orderItem->save();

                $productData = Product::find($item->id);
                if($productData->track_qty=='Yes'){
                    $productData->qty = $productData->qty - $item->qty;
                    $productData->save();
                }
            }
            orderEmail($order->id,'customer');

            session()->flash('create-success','You have successfully placed your order');

            Cart::destroy();

            session()->forget('code');

            return response()->json([
                'status' => true,
                'orderId' => $order->id,
                'message'=> 'You have successfully placed your order'
            ]);
        }else{

        }
    }
    public function thankyou($id){
        return view('front.thanks',[
            'id' => $id,
        ]);
    }
    public function getOrderSummary(Request $request){
        if($request->country_id >0){

            $subTotal = Cart::subtotal(2,'.','');
            $discountString = '';
            $discount = 0;

            //applying an discount
            if(session()->has('code')){
                $code = session()->get('code');
                $discountString ='<div class="mt-4" id="discount-response">
                <strong>' .$code->code. '</strong>
                <a class="btn btn-sm btn-danger" id="remove-discount"><i class="fa fa-times"></i></a>
                </div>';

                if($code->type == 'percent'){
                    $discount = $subTotal*($code->discount_amount/100);
                    // $grandTotal = $subTotal-$discount;
                }else{
                    $discount = $code->discount_amount;
                }
            }


            $shippingInfo = ShippingCharge::where('country_id', $request->country_id)->first();

            $totalQty = 0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            if($shippingInfo!=null){

                $shippingCharge = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subTotal-$discount)+$shippingCharge;
                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal,2),
                    'discount' => number_format($discount,2),
                    'discountString' => $discountString ,
                    'shippingCharge' => number_format($shippingCharge,2),
                ]);

            }else{

                $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();

                $shippingCharge = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subTotal-$discount)+$shippingCharge;
                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal,2),
                    'discount' => number_format($discount,2),
                    'discountString' => $discountString ,
                    'shippingCharge' => number_format($shippingCharge,2),
                ]);
            }

        }else{

            $subTotal = Cart::subtotal(2,'.','');
            $discountString = '';
            $discount = 0;
            //applying an discount
            if(session()->has('code')){
                $code = session()->get('code');

                $discountString ='<div class="mt-4" id="discount-response">
                <strong>'.$code->code.'</strong>
                <a class="btn btn-sm btn-danger" id="remove-discount"><i class="fa fa-times"></i></a>
                </div>';
                if($code->type == 'percent'){
                    $discount = $subTotal*($code->discount_amount/100);
                    // $grandTotal = $subTotal-$discount;
                }else{
                    $discount = $code->discount_amount;
                }
            }


            $grandTotal = $subTotal-$discount;

            return response()->json([
                'status' => true,
                'grandTotal' => number_format($grandTotal,2),
                'discount' => number_format($discount,2),
                'discountString' => $discountString,
                'shippingCharge' => number_format(0,2),
            ]);

        }
    }

    public function applyDiscount(Request $request){
        $code = DiscountCoupon::where('code',$request->code)->first();

        if($code == null){
            return response()->json([
                'status' => false,
                'message'=> 'Invalid Discount Coupon'
            ]);
        }

        $now = Carbon::now();

        // echo $now->format('Y-m-d H:i:s');

        if($code->starts_at != ""){
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s',$code->starts_at);

            if($now->lt($startDate)){
                return response()->json([
                   'status' => false,
                   'message'=> 'Invalid Discount Coupon'
                ]);
            };
        }

        if($code->expires_at != ""){
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s',$code->expires_at);

            if($now->gt($endDate)){
                return response()->json([
                   'status' => false,
                   'message'=> 'Invalid Discount Coupon'
                ]);
            };
        }

        if($code->max_uses>0){
            //check max use
            $couponUsed = Order::where('coupon_code_id',$code->id)->count();

            if($couponUsed >= $code->max_uses){
                return response()->json([
                    'status' => false,
                    'message'=> 'Invalid Discount Coupon'
                ]);
            }
        }

        if($code->max_uses_user>0){
            //check max use per user
            $couponUsedByUser = Order::where(['coupon_code_id'=>$code->id,'user_id'=>Auth::user()->id])->count();

            if($couponUsedByUser >= $code->max_uses_user){
                return response()->json([
                    'status' => false,
                    'message'=> 'You have already used this coupon'
                ]);
            }
        }

        //min amount to use coupon check
        $subTotal = Cart::subtotal(2,'.','');
        if($code->min_amount > 0){
            if($subTotal < $code->min_amount){
                return response()->json([
                   'status' => false,
                   'message'=> 'Your minimun amount must be $'.$code->min_amount.' to use this coupon.'
                ]);
            }
        }

        session()->put('code',$code);

        return $this->getOrderSummary($request);
    }

    public function removeCoupon(Request $request){
        session()->forget('code');
        return $this->getOrderSummary($request);
    }
}

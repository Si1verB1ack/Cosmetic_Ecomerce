<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
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
                session()->flash("success",$message);

            }else{

                $message = 'Request qty('.$qty.') is not available in stock';
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

        if(Cart::count()==0){
            return redirect()->route('front.cart');
        }

        if(Auth::check()==false){
            if(!session()->has('url.intended')){
                session(['url.intended' => url()->current()]);
            }

            return redirect()->route('account.login');
        }

        $customerAddress = CustomerAddress::where('user_id', Auth::user()->id)->first();

        session()->forget('url.intended');

        $countries = Country::orderBy('name', 'ASC')->get();

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
            $grandTotal=Cart::subtotal(2,'.','')+$totalShippingCharge;
        }else{
            $grandTotal=Cart::subtotal(2,'.','');
            $totalShippingCharge =0;
        }

        // dd($customerAddress);

        return view('front.checkout',[
            'countries' => $countries,
            'customerAddress' => $customerAddress,
            'totalShippingCharge' => $totalShippingCharge,
            'grandTotal' => $grandTotal,
        ]);
    }

    public function processCheckout(Request $request){

        // apply valiation
        $validator = Validator::make($request->all(),[
            'first_name' => 'required|min:5',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required|min:30',
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


            $shipping = 0;
            $discount = 0;
            $subTotal = Cart::subtotal(2,'.','');
            $grandTotal = $subTotal+$shipping;

            //calculate shipping
            $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();

            $totalQty = 0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            if($shippingInfo!=null){

                $shipping = $totalQty*$shippingInfo->amount;
                $grandTotal = $subTotal+$shipping;

            }else{

                $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();
                $shipping = $totalQty*$shippingInfo->amount;
                $grandTotal = $subTotal+$shipping;
            }

            $order = new Order;
            $order->subtotal = $subTotal;
            $order->shipping = $shipping;
            $order->grand_total = $grandTotal;
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
            }
            session()->flash('create-success','You have successfully placed your order');

            Cart::destroy();

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

            $shippingInfo = ShippingCharge::where('country_id', $request->country_id)->first();

            $totalQty = 0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            if($shippingInfo!=null){

                $shippingCharge = $totalQty*$shippingInfo->amount;
                $grandTotal = $subTotal+$shippingCharge;
                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal,2),
                    'shippingCharge' => number_format($shippingCharge,2),
                ]);

            }else{

                $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();

                $shippingCharge = $totalQty*$shippingInfo->amount;
                $grandTotal = $subTotal+$shippingCharge;
                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal,2),
                    'shippingCharge' => number_format($shippingCharge,2),
                ]);
            }

        }else{

            $subTotal = Cart::subtotal(2,'.','');
            $grandTotal = $subTotal;

            return response()->json([
                'status' => true,
                'grandTotal' => number_format($grandTotal,2),
                'shippingCharge' => number_format(0,2),
            ]);

        }
    }
}

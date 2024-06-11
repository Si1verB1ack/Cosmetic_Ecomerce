<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;

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
                echo "Cart is empty";
                Cart::add($product->id, $product->title, 1, $product->price,
                ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
                $status = true;
                $message = $product->title.' Added in cart successfully';
            }else{
                $status = false;
                $message = $product->title.' Already Added in cart';
            }
        }else{
            Cart::add($product->id, $product->title, 1, $product->price,
            ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
            $status = true;
            $message = $product->title.' Added in cart successfully';
        }
        // session()->flash('create-success',$message);

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
}

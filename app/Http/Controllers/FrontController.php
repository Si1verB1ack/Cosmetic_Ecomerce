<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontController extends Controller
{
    public function index(){

        $product = Product::where('is_featured','Yes')
            ->orderBy('id','DESC')
            ->take(8)
            ->where('status',1)
            ->get();
        $data['featuredProducts'] = $product;

        $latestProducts = Product::orderBy('id','DESC')
            ->where('status',1)
            ->take(8)
            ->get();
        $data['latestProducts'] = $latestProducts;

        return view('front.home',$data);
    }

    public function addToWishlist(Request $request){
        if(Auth::check()==false){
            session(['url.intended'=>url()->previous()]);
            return response()->json([
               'status' =>  false,
               'message'=> 'Please login first'
            ]);
        }
        $product = Product::where('id',$request->id)->first();
        if(empty($product)){
            session()->flash("not-found","Record not found");
            return response()->json([
               'status' => false,
               'message'=> 'Product not found'
            ]);
        }
        $productImage = $product->product_images->first();
        $imageUrl = !empty($productImage) ? asset('uploads/product/small/' . $productImage->image) : asset('admin-assets/img/default-150x150.png');

        Wishlist::updateOrCreate(
            [
                'user_id' => Auth::user()->id,
                'product_id' => $request->id,
            ],
            [
                'user_id' => Auth::user()->id,
                'product_id' => $request->id,
            ]
        );

        // $wishlist = new Wishlist;
        // $wishlist->user_id = Auth::user()->id;
        // $wishlist->product_id = $request->id;
        // $wishlist->save();

        return response()->json([
            'status' => true,
            'message'=> $product->title.' has been added to your wishlist',
            'image_url' => $imageUrl,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null,$subCategorySlug = null){
        $categorySelected='';
        $subCategorySelected='';
        $brandsArray = [];

        $categories = Category::orderBy('name', 'ASC')->with('sub_category')->where('status', 1)->get();
        $brands = Brands::orderBy('name', 'ASC')->where('status', 1)->get();

        $products = Product::where('status', 1);

        // Apply filters
        if(!empty($categorySlug)){
            $category=Category::where('slug', $categorySlug)->first();
            $products = $products->where('category_id', $category->id);
            $categorySelected=$category->id;
        }
        if(!empty($SubCategorySlug)){
            $subCategory=SubCategory::where('slug', $subCategorySlug)->first();
            $products = $products->where('sub_category_id', $subCategory->id);
            $subCategorySelected=$subCategory->id;
        }
        if(!empty($request->get('brand'))){
            $brandsArray = explode(',',$request->get('brand'));
            $products = $products->whereIn('brand_id', $brandsArray);
        }
        if($request->get('price_max')!=''&&$request->get('price_min')!=''){
            if($request->get('price_max')==1000){
                $products = $products->whereBetween('price', [intval($request->get('price_min')), 1000000]);
            }else{
                $products = $products->whereBetween('price', [intval($request->get('price_min')), intval($request->get('price_max'))]);
            }
        }

        // dd($categories = Category::latest());
        if(!empty($request->get('search'))){
            $products = $products->where('title','like','%'. $request->get('search'). '%');
        }

        if($request->get('sort') != ''){
            if($request->get('sort')=='lateset'){
                $products = $products->orderBy('id', 'DESC');
            }else if($request->get('sort')=='price_asc'){
                $products = $products->orderBy('price','ASC');
            }else{
                $products = $products->orderBy('price','DESC');
            }
        }else{
            $products = $products->orderBy('id', 'DESC');
        }

        $products = $products->paginate(6);

        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['products'] = $products;
        $data['categorySelected'] = $categorySelected;
        $data['subCategorySelected'] = $subCategorySelected;
        $data['brandsArray'] = $brandsArray;
        $data['priceMax'] = (intval($request->get('price_max'))==0) ? 1000 : intval($request->get('price_max'));
        $data['priceMin'] = intval($request->get('price_min'));
        $data['sort'] = $request->get('sort');

        return view('front.shop',$data);
    }

    public function product($slug){
        $product = Product::where('slug', $slug)->with('product_images')->first();
        if($product == null){
            abort(404);
        }

        //fetches the related products
        $relatedProucts = [];
        if($product->related_products!=''){
            $productArray = explode(',',$product->related_products);
            $relatedProucts = Product::whereIn('id',$productArray)->where('status',1)->get();
        }

        $data['product'] = $product;
        $data['relatedProucts'] = $relatedProucts;
        return view('front.product',$data);
    }

}

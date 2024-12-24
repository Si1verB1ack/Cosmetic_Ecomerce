<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductRating;
use App\Models\ProductVariant;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
    {
        $categorySelected = '';
        $subCategorySelected = '';
        $brandsArray = [];

        $categories = Category::orderBy('name', 'ASC')->with('sub_category')->where('status', 1)->get();
        $brands = Brands::orderBy('name', 'ASC')->where('status', 1)->get();

        $products = Product::where('status', 1);

        // Apply filters
        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
            $products = $products->where('category_id', $category->id);
            $categorySelected = $category->id;
        }
        if (!empty($SubCategorySlug)) {
            $subCategory = SubCategory::where('slug', $subCategorySlug)->first();
            $products = $products->where('sub_category_id', $subCategory->id);
            $subCategorySelected = $subCategory->id;
        }
        if (!empty($request->get('brand'))) {
            $brandsArray = explode(',', $request->get('brand'));
            $products = $products->whereIn('brand_id', $brandsArray);
        }
        if ($request->get('price_max') != '' && $request->get('price_min') != '') {
            if ($request->get('price_max') == 1000) {
                $products = $products->whereBetween('price', [intval($request->get('price_min')), 1000000]);
            } else {
                $products = $products->whereBetween('price', [intval($request->get('price_min')), intval($request->get('price_max'))]);
            }
        }

        // dd($categories = Category::latest());
        if (!empty($request->get('search'))) {
            $products = $products->where('title', 'like', '%' . $request->get('search') . '%');
        }

        if ($request->get('sort') != '') {
            if ($request->get('sort') == 'lateset') {
                $products = $products->orderBy('id', 'DESC');
            } else if ($request->get('sort') == 'price_asc') {
                $products = $products->orderBy('price', 'ASC');
            } else {
                $products = $products->orderBy('price', 'DESC');
            }
        } else {
            $products = $products->orderBy('id', 'DESC');
        }

        $products = $products->paginate(6);

        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['products'] = $products;
        $data['categorySelected'] = $categorySelected;
        $data['subCategorySelected'] = $subCategorySelected;
        $data['brandsArray'] = $brandsArray;
        $data['priceMax'] = (intval($request->get('price_max')) == 0) ? 1000 : intval($request->get('price_max'));
        $data['priceMin'] = intval($request->get('price_min'));
        $data['sort'] = $request->get('sort');

        return view('front.shop', $data);
    }

    public function product($slug)
    {

        $product = Product::where('slug', $slug)
            ->withCount('product_ratings')
            ->withSum('product_ratings', 'rating')
            ->with([
                'product_images',
                'product_ratings',
                'variants.color', // Load variants with their colors
                'variants.size' // Load variants with their colors
            ])
            ->firstOrFail();


        if ($product == null) {
            abort(404);
        }

        //fetches the related products
        $relatedProucts = [];
        if ($product->related_products != '') {
            $productArray = explode(',', $product->related_products);
            $relatedProucts = Product::whereIn('id', $productArray)->where('status', 1)->get();
        }


        $data['product'] = $product;
        $data['relatedProucts'] = $relatedProucts;

        $avgRating = '0.00';
        $avgRatingPercentage = 0;

        if ($product->product_ratings_count > 0) {
            $avgRating = number_format($product->product_ratings_sum_rating / $product->product_ratings_count, 2);
            $avgRatingPercentage = ($avgRating * 100) / 5;
        }

        $data['avgRating'] = $avgRating;
        $data['avgRatingPercentage'] = $avgRatingPercentage;
        $data['product_variant'] = null;


        return view('front.product', $data);
    }

    public function saveRating(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|min:5',
            'email' => 'required|email',
            'comment' => 'required|min:10',
            'rating' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $existingRating = ProductRating::where('email', $request->email)
            ->where('product_id', $request->id)
            ->exists();

        if ($existingRating > 0) {

            session()->flash('error', 'You have already rate this product');

            return response()->json([
                'status' => true,
                'message' => 'You have already rate this product',
            ]);
        };

        $productRating = new ProductRating();
        $productRating->product_id = $request->id;
        $productRating->username = $request->name;
        $productRating->email = $request->email;
        $productRating->comment = $request->comment;
        $productRating->rating = $request->rating;
        $productRating->status = 0;
        $productRating->save();

        session()->flash('success', 'Thank you for your ratings.');

        return response()->json([
            'status' => true,
            'message' => 'Thank you for your ratings.',
        ]);
    }
    public function getVariantDetails($slug, Request $request)
    {

        $product = Product::where('slug', $slug)
            ->withCount('product_ratings')
            ->withSum('product_ratings', 'rating')
            ->with([
                'product_images',
                'product_ratings',
                'variants.color', // Load variants with their colors
                'variants.size' // Load variants with their colors
            ])
            ->firstOrFail();


        if ($product == null) {
            abort(404);
        }

        //fetches the related products
        $relatedProucts = [];
        if ($product->related_products != '') {
            $productArray = explode(',', $product->related_products);
            $relatedProucts = Product::whereIn('id', $productArray)->where('status', 1)->get();
        }


        $data['product'] = $product;
        $data['relatedProucts'] = $relatedProucts;

        $avgRating = '0.00';
        $avgRatingPercentage = 0;

        if ($product->product_ratings_count > 0) {
            $avgRating = number_format($product->product_ratings_sum_rating / $product->product_ratings_count, 2);
            $avgRatingPercentage = ($avgRating * 100) / 5;
        }

        $data['avgRating'] = $avgRating;
        $data['avgRatingPercentage'] = $avgRatingPercentage;

        // Retrieve color_id, size_id, and variant_id from the query parameters
        $variantId = $request->input('variant_id');
        $colorId = $request->input('color');  // Assuming 'color' will be color_id in the query string
        $sizeId = $request->input('size');    // Assuming 'size' will be size_id in the query string

        // dd($request->input('variant_id'));

        // Find the variant based on the provided parameters
        $variant = ProductVariant::with('images')
            ->where('product_id', $request->input('productId'))
            ->when($colorId, function ($query) use ($colorId) {
                return $query->where('color_id', $colorId);
            })
            ->when($sizeId, function ($query) use ($sizeId) {
                return $query->where('size_id', $sizeId);
            })
            ->first();


        // If no matching variant is found, return with an error message
        if (!$variant) {
            return redirect()->back()->with('error', 'Variant not found');
        }

        // Get the related color and size names from their respective tables
        $color = $variant->color;  // Assuming your ProductVariant has a 'color' relation
        $size = $variant->size;    // Assuming your ProductVariant has a 'size' relation
        $variantImages = $variant->images->first(); // Assuming your ProductVariant has a 'size' relation

        $data['color'] = $color;
        $data['size'] = $size;
        $data['product_variant'] = $variant;
        $data['variantImages'] = $variantImages;

        // dd($variantImages->image); //

        // Return a view with the product, variant, color, and size details
        return view('front.product', $data);
    }
}

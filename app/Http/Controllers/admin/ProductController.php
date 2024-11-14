<?php

namespace App\Http\Controllers\admin;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FacebookPostController; // Adjust to the correct namespace
use App\Mail\NewProductEmail;
use App\Models\Brands;
use App\Models\SubCategory;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductRating;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\TempImage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;


class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::latest('id')->with(['product_images', 'variants.color', 'variants.size']); // Eager load color and size
        if (!empty($request->get('keyword'))) {
            $products = $products->where('title', 'like', '%' . $request->get('keyword') . '%');
        }
        $products = $products->paginate();
        // dd($products);
        $data['products'] = $products;
        return view('admin.products.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [];
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brands::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        return view('admin.products.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $facebookController = new FacebookPostController();

        // dd($request->image_array);
        // exit();
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
            // 'qty' => 'required|numeric',
        ];

        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {
            $product = new Product();
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->shipping_returns = $request->shipping_returns;
            $product->short_description = $request->short_description;
            $product->related_products = (!empty($request->related_products) ? implode(',', $request->related_products) : ' ');
            $product->save();

            if (!empty($request->image_array)) {
                $manager = new ImageManager(new Driver());
                foreach ($request->image_array as $temp_image_id) {
                    $tempImageInfo = TempImage::find($temp_image_id);
                    $extArray = explode('.', $tempImageInfo->name);
                    $ext = last($extArray);

                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image = 'NULL';
                    $productImage->save();

                    $imageName = $product->id . '-' . $productImage->id . '-' . time() . '.' . $ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    //genderate prodcut thumnail

                    //large image
                    $sourcePath = public_path() . '/temp/' . $tempImageInfo->name;
                    $destPath = public_path() . '/uploads/product/large/' . $imageName;
                    $image = $manager->read($sourcePath);
                    $image->scale(1400);
                    $image->save($destPath);

                    //small image
                    $destPath = public_path() . '/uploads/product/small/' . $imageName;
                    // $forfb = 'http://localhost/uploads/product/small/' . $imageName;
                    $image = $manager->read($sourcePath);
                    $image->scale(300);
                    $image->save($destPath);
                }

                $mailData = [
                    'mail_subject' => 'Check out our new product!',
                    'name' => $request->title,
                    'price' => $request->price
                ];

                // call the api to post to the failbook

                Mail::to('user@example.com')->send(new NewProductEmail($mailData, $destPath));

                // Call the create method
                $response = $facebookController->create(
                    "Check out our new product {$request->title}! Only $ {$request->price}!!",
                    $destPath
                );
            } else {
                $response = $facebookController->create(
                    "Check out our new product {$request->title}! Only $ {$request->price}!!"
                );
            }



            session()->flash('create-success', 'Product created successfully');

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id, Request $request)
    {
        $product = Product::find($id);
        if (empty($product)) {
            return redirect()->route('products.index')->with("not-found", "Record not found");
        }
        $productImages = ProductImage::where('product_id', $product->id)->get();
        $subCategories = SubCategory::where('category_id', $product->category_id)->get();
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brands::orderBy('name', 'ASC')->get();

        //fetches the related products
        $relatedProucts = [];
        if ($product->related_products != '') {
            $productArray = explode(',', $product->related_products);
            $relatedProucts = Product::whereIn('id', $productArray)->with('product_images')->get();
        }



        $data = [];
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['products'] = $product;
        $data['subCategories'] = $subCategories;
        $data['productImages'] = $productImages;
        $data['relatedProucts'] = $relatedProucts;
        // dd($brand);
        return view('admin.products.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        $product = Product::find($id);

        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug,' . $product->id . ',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,' . $product->id . ',id',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
            // 'qty' => 'required|numeric',
        ];

        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {

            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->shipping_returns = $request->shipping_returns;
            $product->short_description = $request->short_description;
            $product->related_products = (!empty($request->related_products) ? implode(',', $request->related_products) : ' ');
            $product->save();

            session()->flash('create-success', 'Product updated successfully');

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Request $request)
    {
        $product = Product::find($id);

        if (empty($product)) {
            session()->flash("not-found", "Record not found");
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Record not found'
            ]);
        }

        $productImage = ProductImage::where('product_id', $id)->get();
        // dd($productImage);
        if (!empty($productImage)) {
            foreach ($productImage as $image) {
                File::delete(public_path() . '/uploads/product/large/' . $image->image);
                File::delete(public_path() . '/uploads/product/small/' . $image->image);
            }
            ProductImage::where('product_id', $id)->delete();
        }

        $product->delete();

        session()->flash('delete-success', 'Product delete successfully');

        return response()->json([
            'status' => true,
            'message' => 'Product delete successfully'
        ]);
    }

    public function getProducts(Request $request)
    {
        $tempProduct = [];
        if ($request->term != "") {
            $products = Product::where('title', 'like', '%' . $request->term . '%')->get();

            if ($products != null) {
                foreach ($products as $product) {
                    $tempProduct[] = array(
                        'id' => $product->id,
                        'text' => $product->title
                    );
                }
            }
        }

        return response()->json([
            'tags' => $tempProduct,
            'status' => true
        ]);
    }
    public function productRatings(Request $request)
    {
        $ratings = ProductRating::select('product_ratings.*', 'products.title as ProductTitle')->orderBy('product_ratings.created_at', 'DESC');
        $ratings = $ratings->leftJoin('products', 'products.id', 'product_ratings.product_id');

        if (!empty($request->get('keyword'))) {
            $ratings = $ratings->orWhere('products.title', 'like', '%' . $request->get('keyword') . '%');
            $ratings = $ratings->orWhere('product_ratings.username', 'like', '%' . $request->get('keyword') . '%');
        }
        $ratings = $ratings->paginate(10);


        return view('admin.products.ratings', [
            'ratings' => $ratings
        ]);
    }

    public function changeRatingStatus(Request $request)
    {
        $productRating = ProductRating::find($request->id);
        $productRating->status = $request->status;
        $productRating->save();


        session()->flash('create-success', 'Product status changed successfully');

        return response()->json([
            'status' => true,
            'message' => 'Product status changed successfully'
        ]);
    }
}

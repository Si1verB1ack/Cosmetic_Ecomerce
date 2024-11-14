<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Color;
use App\Models\VariantImage;
use App\Models\Size;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\File;
use App\Models\TempImage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Validation\Rule;


class ProductVariantController extends Controller
{
    // Show form for creating a new variant for a specific product
    public function create($productId)
    {

        $product = Product::findOrFail($productId);
        $colors = Color::all(); // Fetch all colors from the database
        $sizes = Size::all();   // Fetch all sizes from the database
        $variants = $product->variants()->with('images')->get();

        return view('admin.products.variants.create', compact('product', 'colors', 'sizes', 'variants'));
    }

    // Store a new variant for the given product
    public function store(Request $request, $productId)
    {
        // Validate the incoming request data
        $request->validate([
            'color_id' => [
                'required',
                'exists:colors,id',
                Rule::unique('product_variants')
                    ->where('size_id', $request->size_id)
                    ->where('product_id', $productId),
            ],
            'size_id' => 'required|exists:sizes,id',
            'qty' => 'required|integer|min:0',
            'sku' => 'required|string|max:255|unique:product_variants,sku',
            'price_adjustment' => 'nullable|numeric',
            'image_id' => 'nullable|exists:temp_images,id',
        ], [
            'color_id.unique' => 'This color and size combination already exists for the selected product. try something else',
        ]);



        // Find the product by its ID
        $product = Product::findOrFail($productId);

        // Create the product variant using the validated data
        $variant = $product->variants()->create($request->only([
            'color_id',
            'size_id',
            'qty',
            'sku',
            'price_adjustment',
        ]));

        if (!empty($request->image_id)) {
            $manager = new ImageManager(new Driver());

            $tempImageInfo = TempImage::find($request->image_id);

            if ($tempImageInfo) {
                // Extract the extension from the temporary image name
                $extArray = explode('.', $tempImageInfo->name);
                $ext = last($extArray);

                // Create a new variant image record in the database
                $variantImage = new VariantImage();
                $variantImage->product_variant_id = $variant->id; // Link to the created variant
                $variantImage->image = 'NULL';
                $variantImage->save();

                // Generate a unique image name
                $imageName = $variant->id . '-' . $variantImage->id . '-' . time() . '.' . $ext;

                // Update the image path in the database
                $variantImage->image = $imageName;
                $variantImage->save();

                // Define paths for the source image and destination sizes
                $sourcePath = public_path('temp/' . $tempImageInfo->name);
                $largeImagePath = public_path('uploads/product/large/variants/' . $imageName);
                $smallImagePath = public_path('uploads/product/small/variants/' . $imageName);

                // Ensure the destination directories exist
                if (!file_exists(public_path('uploads/product/large/variants'))) {
                    mkdir(public_path('uploads/product/large/variants'), 0777, true);
                }
                if (!file_exists(public_path('uploads/product/small/variants'))) {
                    mkdir(public_path('uploads/product/small/variants'), 0777, true);
                }

                // Large image (1400px)
                $largeImage = $manager->read($sourcePath)->scale(1400);
                $largeImage->save($largeImagePath);

                // Small image (300px)
                $smallImage = $manager->read($sourcePath)->scale(300);
                $smallImage->save($smallImagePath);
            }
        }

        // Flash a success message to the session
        session()->flash('create-success', 'Product variant created successfully');

        // Return a JSON response with status and message
        return redirect()->route('products.index');
    }



    // Show form for editing an existing variant
    // Show form for editing an existing variant
    public function edit($productId, $variantId)
    {
        $product = Product::findOrFail($productId);
        $variant = ProductVariant::where('product_id', $productId)->with('images')->findOrFail($variantId);
        $colors = Color::all(); // Fetch all colors from the database
        $sizes = Size::all();   // Fetch all sizes from the database

        return view('admin.products.variants.edit', compact('product', 'variant', 'colors', 'sizes'));
    }


    // Show form for editing an existing variant
    // Show form for editing an existing variant
    public function update(Request $request, $productId, $variantId)
    {
        // Validation for required fields and image constraints
        $request->validate([
            'color_id' => 'sometimes|required|exists:colors,id',
            'size_id' => 'sometimes|required|exists:sizes,id',
            'qty' => 'sometimes|required|integer|min:0',
            'sku' => 'sometimes|required|string|max:255|unique:product_variants,sku,' . $variantId,
            'price_adjustment' => 'nullable|numeric',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif', // Validation for images
        ]);



        // Fetch the variant
        $variant = ProductVariant::where('product_id', $productId)->findOrFail($variantId);

        $variantImage = VariantImage::where('product_variant_id', $variantId)->first();

        if ($variantImage) {
            $oldImage = $variantImage->image;  // Accessing the 'image' field if it's not null
        } else {
            $oldImage = null;  // No image found for this variant
        }
        // dd($oldImage);


        // Update basic variant fields
        $variant->update($request->only(['color_id', 'size_id', 'qty', 'sku', 'price_adjustment']));

        // dd($request->image_id)
        if (!empty($request->image_id)) {
            $manager = new ImageManager(new Driver());

            $tempImageInfo = TempImage::find($request->image_id);

            if ($tempImageInfo) {
                try {
                    // Extract the extension from the temporary image name
                    $extArray = explode('.', $tempImageInfo->name);
                    $ext = last($extArray);

                    // Generate a unique image name for the new image
                    $newImageName = $variant->id . '-' . time() . '.' . $ext;

                    // Define paths for the source image and destination sizes
                    $sourcePath = public_path('temp/' . $tempImageInfo->name);
                    $largeImagePath = public_path('uploads/product/large/variants/' . $newImageName);
                    $smallImagePath = public_path('uploads/product/small/variants/' . $newImageName);

                    // Ensure the destination directories exist
                    if (!file_exists(public_path('uploads/product/large/variants'))) {
                        mkdir(public_path('uploads/product/large/variants'), 0777, true);
                    }
                    if (!file_exists(public_path('uploads/product/small/variants'))) {
                        mkdir(public_path('uploads/product/small/variants'), 0777, true);
                    }

                    // Copy the image to the final destination and create different image sizes
                    File::copy($sourcePath, $largeImagePath);

                    $largeImage = $manager->read($sourcePath)->scale(1400);
                    $largeImage->save($largeImagePath);

                    $smallImage = $manager->read($sourcePath)->scale(300);
                    $smallImage->save($smallImagePath);

                    // Assign the new image name to the variant
                    $variantImage->image = $newImageName;
                    $variantImage->save();

                    // Delete the old image if it exists
                    if ($oldImage) {
                        File::delete(public_path('uploads/product/large/variants/' . $oldImage));
                        File::delete(public_path('uploads/product/small/variants/' . $oldImage));
                    }
                } catch (\Exception $e) {
                    return back()->withErrors('Error processing the image: ' . $e->getMessage());
                }
            }
        } else {
            // If image_id is null, create the variant image with an empty string for the image field
            $variantImage = new VariantImage();
            $variantImage->product_variant_id = $variant->id;
            $variantImage->image = ''; // Assign empty string instead of null
            $variantImage->save();
        }


        // Flash a success message to the session
        session()->flash('create-success', 'Product variant updated successfully');

        // Return a JSON response with status and message
        return redirect()->route('products.index');
    }


    // Delete an existing variant
    public function destroy($productId, $variantId)
    {
        try {
            // Find the variant by its product_id and variant_id
            $variant = ProductVariant::where('product_id', $productId)->findOrFail($variantId);

            // Optionally, delete related images (if they exist) before deleting the variant
            $variantImage = VariantImage::where('product_variant_id', $variantId)->first();
            if ($variantImage) {
                // Delete images from the filesystem if necessary
                File::delete(public_path('uploads/product/large/variants/' . $variantImage->image));
                File::delete(public_path('uploads/product/small/variants/' . $variantImage->image));
                $variantImage->delete();
            }

            // Delete the variant itself
            $variant->delete();

            // Return a redirect with a success message if it's not an AJAX request
            session()->flash('delete-success', 'Product varaint delete successfully');

            return response()->json([
                'status' => true,
                'message' => 'Product varaint delete successfully'
            ]);
        } catch (\Exception $e) {
            // Handle errors and return an error message
            if (request()->ajax()) {
                return response()->json(['status' => false, 'message' => 'Error deleting the variant.']);
            }

            // In case of an error with non-AJAX requests, redirect with an error message
            return redirect()->route('product.list')->with('error', 'Error deleting the variant.');
        }
    }
}

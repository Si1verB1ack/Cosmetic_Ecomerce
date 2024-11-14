<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CategoryController extends Controller
{
    public function index(Request $request){
        $categories = Category::latest('id');
        // dd($categories = Category::latest());
        if(!empty($request->get('keyword'))){
            $categories = $categories->where('name','like','%'. $request->get('keyword'). '%');
        }
        $categories = $categories->paginate(10);

        return view('admin.category.list',compact('categories'));
    }
    public function create(){
        return view('admin.category.create');
    }
    public function store(Request $request){
        $validator = Validator::make(request()->all(), [
            'name'=> 'required',
            'slug'=> 'required|unique:categories',
        ]) ;
        if ($validator->passes()) {
            // dd($request->image_id);
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;
            $category->save();

            if(!empty($request->image_id)){
                $manager = new ImageManager(new Driver());
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath,$dPath);

                $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
                $img = $manager->read($sPath);
                $img->cover(450, 600);
                $img->save($dPath);


                $category->image = $newImageName;
                $category->save();
            }

            session()->flash('create-success','Category created successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Category created successfully'
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors'=> $validator->errors()
            ]);
        }

    }
    public function edit($categoryId, Request $request){
        $category = Category::find($categoryId);
        // dd($category);
        if(empty($category)){
            return redirect()->route('categories.index')->with("not-found","Record not found");
        }
        // dd($category);
        return view('admin.category.edit',compact('category'));
    }
    public function update($categoryId, Request $request){
        // dd($request);
        $category = Category::find($categoryId);

        if(empty($category)){
            // return redirect()->route('categories.index');
            session()->flash("not-found","Record not found");
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message'=> 'Record not found'
            ]);
        }   
        // if(empty($category)){

        //     session()->flash("not-found","Category not found");

        //     return response()->json([
        //         'status' => false,
        //         'notFound'=> true,
        //         'message' => 'Category not found'
        //     ]);
        // }

        $validator = Validator::make(request()->all(), [
            'name'=> 'required',
            'slug'=> 'required|unique:categories,slug,'.$category->id.',id',
        ]) ;
        if ($validator->passes()) {
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;
            $category->save();

            $oldImage = $category->image;

            //image save
            if(!empty($request->image_id)){
                $manager = new ImageManager(new Driver());
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'-'.time().'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath,$dPath);

                $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
                $img = $manager->read($sPath);
                $img->coverDown(450, 600,'center');
                $img->save($dPath);
                $category->image = $newImageName;
                $category->save();

                // delete old image
                File::delete(public_path().'/uploads/category/thumb/'.$oldImage);
                File::delete(public_path().'/uploads/category/'.$oldImage);
            }

            session()->flash('update-success','Category update successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Category update successfully'
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors'=> $validator->errors()
            ]);
        }
    }
    public function destroy($categoryId, Request $request){
        $category = Category::find($categoryId);
        if(empty($category)){
            // return redirect()->route('categories.index');
            session()->flash("not-found","Record not found");
            return response()->json([
                'status' => true,
                'message'=> 'Record not found'
            ]);
        }
        File::delete(public_path().'/uploads/category/thumb/'.$category->image);
        File::delete(public_path().'/uploads/category/'.$category->image);

        $category->delete();

        session()->flash('delete-success','Category delete successfully'    );

        return response()->json([
            'status' => true,
            'message'=> 'Category delete successfully'
        ]);
    }
}

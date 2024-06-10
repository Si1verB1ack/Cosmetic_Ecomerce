<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $subCategories = SubCategory::select('sub_categories.*','categories.name as categoryName')
                            ->latest('sub_categories.id')
                            ->leftJoin('categories','categories.id','sub_categories.category_id');
        if(!empty($request->get('keyword'))){
            $subCategories = $subCategories->where('sub_categories.name','like','%'. $request->get('keyword'). '%');
            $subCategories = $subCategories->orwhere('categories.name','like','%'. $request->get('keyword'). '%');
        }
        $subCategories = $subCategories->paginate(10);

        return view('admin.sub_category.list',compact('subCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        return view('admin.sub_category.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'name'=> 'required',
            'slug'=> 'required|unique:sub_categories',
            'category'=> 'required',
            'status'=> 'required',
        ]) ;
        if ($validator->passes()) {

            $subCategory = new SubCategory();
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category;
            $subCategory->showHome = $request->showHome;
            $subCategory->save();

            session()->flash('create-success','Sub category created successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Sub category created successfully'
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors'=> $validator->errors()
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id, Request $request)
    {
        $subCategory = SubCategory::find($id);
        if(empty($subCategory)){
            return redirect()->route('sub-categories.index')
                    ->with("not-found","Record not found");
        }

        $categories = Category::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        $data['subCategory']=$subCategory;

        return view('admin.sub_category.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        $subCategory = SubCategory::find($id);

        if(empty($subCategory)){
            // return redirect()->route('categories.index');
            session()->flash("not-found","Record not found");
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message'=> 'Record not found'
            ]);
        }

        $validator = Validator::make(request()->all(), [
            'name'=> 'required',
            'slug'=> 'required|unique:categories,slug,'.$subCategory->id.',id',
            'category'=> 'required',
            'status'=> 'required',
        ]) ;
        if ($validator->passes()) {


            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->showHome = $request->showHome;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            session()->flash('update-success','Sub Category update successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Sub Category update successfully'
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors'=> $validator->errors()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $subCategory = SubCategory::find($id);
        if(empty($subCategory)){
            // return redirect()->route('categories.index');
            session()->flash("not-found","Record not found");
            return response()->json([
                'status' => true,
                'message'=> 'Record not found'
            ]);
        }

        $subCategory->delete();

        session()->flash('delete-success','Sub Category delete successfully');

        return response()->json([
            'status' => true,
            'message'=> 'Sub Category delete successfully'
        ]);
    }
}

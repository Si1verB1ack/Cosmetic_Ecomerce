<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandsController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brands::latest('id');
        if(!empty($request->get('keyword'))){
            $brands = $brands->where('name','like','%'. $request->get('keyword'). '%');
        }
        $brands = $brands->paginate(10);

        return view('admin.brands.list',compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'name'=> 'required',
            'slug'=> 'required|unique:brands',
            'status'=> 'required',
        ]) ;

        if ($validator->passes()) {
            $brand = new Brands();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();
            session()->flash('create-success','Brand created successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Brand created successfully'
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
        $brands = Brands::find($id);
        if(empty($brands)){
            return redirect()->route('brand.index')->with("not-found","Record not found");
        }
        // dd($brand);
        return view('admin.brands.edit',compact('brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        $brand = Brands::find($id);

        if(empty($brand)){
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
            'slug'=> 'required|unique:brands,slug,'.$brand->id.',id',
            'status'=> 'required',
        ]);

        if ($validator->passes()) {

            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            session()->flash('update-success','Brand update successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Brand update successfully'
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
        $brand = Brands::find($id);
        if(empty($brand)){
            // return redirect()->route('categories.index');
            session()->flash("not-found","Record not found");
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message'=> 'Record not found'
            ]);
        }

        $brand->delete();

        session()->flash('delete-success','Brand delete successfully');

        return response()->json([
            'status' => true,
            'message'=> 'Brand delete successfully'
        ]);
    }
}

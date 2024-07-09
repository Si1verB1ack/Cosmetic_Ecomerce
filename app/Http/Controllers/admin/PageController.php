<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class PageController extends Controller
{
    // 5 methods index, create, store, edit, update, delete
    public function index(Request $request){

        $pages = Page::latest('id');
        if(!empty($request->get('keyword'))){
            $pages = $pages->where('name','like','%'. $request->get('keyword'). '%');
        }
        $pages = $pages->paginate(10);

        return view('admin.pages.list',compact('pages'));
    }
    public function create(){
        return view('admin.pages.create');
    }
    public function store(Request $request){
        $validator = Validator::make(request()->all(), [
            'name'=> 'required',
            'slug'=> 'required',
        ]) ;

        if ($validator->passes()) {

            $page = new Page();
            $page->name = $request->name;
            $page->slug = $request->slug;
            $page->content = $request->content;
            $page->save();

            session()->flash('create-success','Page created successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Page created successfully'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors'=> $validator->errors()
            ]);
        }
    }
    public function edit($id, Request $request){

        $page = Page::find($id);
        if(empty($page)){
            session()->flash("not-found","Record not found");
            return redirect()->route('admin.pages.index');
        }
        return view('admin.pages.edit',compact('page'));
    }
    public function update($id, Request $request){

        $validator = Validator::make(request()->all(), [
            'name'=> 'required',
            'slug'=> 'required',
        ]) ;

        if ($validator->passes()) {

            $page = Page::where('id',$id);
            $page->name = $request->name;
            $page->slug = $request->slug;
            $page->content = $request->content;
            $page->save();

            session()->flash('create-success','Page created successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Page created successfully'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors'=> $validator->errors()
            ]);
        }

    }

    public function destroy($id, Request $request){

    }


}

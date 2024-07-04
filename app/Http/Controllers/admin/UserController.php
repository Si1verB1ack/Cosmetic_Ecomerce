<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::latest('id');
        if(!empty($request->get('keyword'))){
            $users = $users->where('name','like','%'. $request->get('keyword'). '%')->orWhere('email','like','%'. $request->get('keyword'));
        }
        $users = $users->paginate(10);
        return view('admin.users.list',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'name'=> 'required',
            'email'=> 'required|email|unique:users',
            'password'=> 'required|min:8',
            'phone'=> 'required',
            'status'=> 'required',
        ]) ;

        if ($validator->passes()) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->phone = $request->phone;
            $user->status = $request->status;
            $user->save();
            session()->flash('create-success','user created successfully');

            return response()->json([
                'status' => true,
                'message'=> 'user created successfully'
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
        $user = User::find($id);
        if(empty($user)){
            return redirect()->route('user.index')->with("not-found","Record not found");
        }
        return view('admin.users.edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        $user = User::find($id);

        if(empty($user)){
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
            'email'=> 'required|email|unique:users,email,'.$id.'id',
            'password'=> 'nullable|min:8',
            'phone'=> 'required',
            'status'=> 'required',
        ]);

        if ($validator->passes()) {

            $user->name = $request->name;
            $user->email = $request->email;
            if(!empty($request->password)){
                $user->password = Hash::make($request->password);
            }
            $user->phone = $request->phone;
            $user->status = $request->status;
            $user->save();

            session()->flash('update-success','user update successfully');

            return response()->json([
                'status' => true,
                'message'=> 'user update successfully'
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
        $user = User::find($id);
        if(empty($user)){
            // return redirect()->route('categories.index');
            session()->flash("not-found","Record not found");
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message'=> 'Record not found'
            ]);
        }

        $user->delete();

        session()->flash('delete-success','user delete successfully');

        return response()->json([
            'status' => true,
            'message'=> 'user delete successfully'
        ]);
    }
}

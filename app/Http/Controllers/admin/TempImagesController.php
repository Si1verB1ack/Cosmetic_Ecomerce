<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TempImagesController extends Controller
{
    public function create(Request $request){
        $image = $request->image;

        if(!empty($image)){
            $ext = $image->getClientOriginalExtension();
            $newName = time().'.'.$ext;

            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            $image->move(public_path().'/temp',$newName);

            //Generate thumbnail
            $manager = new ImageManager(new Driver());
            $sourcePath = public_path().'/temp/'.$newName;
            $destPath = public_path().'/temp/thumb/'.$newName;
            $img = $manager->read($sourcePath);
            $img->cover(300, 300);
            $img->save($destPath);

            return response()->json([
                'status'=>true,
                'image_id'=>$tempImage->id,
                'ImagePath'=>asset('/temp/thumb/'.$newName),
                'message'=>'image uploaded successfully'
            ]);
        }
    }
}

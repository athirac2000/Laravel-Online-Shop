<?php

namespace App\Http\Controllers\admin;


use App\Models\TempImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;

class TempImagesController extends Controller
{
    public function create(Request $request){
        $image= $request->image;
        if(!empty($image)){
            $ext = $image->getClientOriginalExtension();
            $newName = time().'.'.$ext;
            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();
            
            $image->move(public_path().'/temp',$newName);

            //Generate thumnails
            $sourcePath = public_path().'/temp/'.$newName; //This is the path where the original uploaded image is temporarily stored. 
            $destPath = public_path().'/temp/thumb/'.$newName; //This is the path where the thumbnail image will be saved.
            $image = Image::make($sourcePath);
            $image->fit(300,275);
            $image->save($destPath);

            return response()->json([
                'status' => true,
                'image_id' =>  $tempImage->id,
                'ImagePath' =>  asset('/temp/thumb/'.$newName),
                'message' => 'image uploaded successfully'
            ]);
        }
    }
}

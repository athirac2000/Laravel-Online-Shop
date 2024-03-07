<?php

namespace App\Http\Controllers\admin;
use Image;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class ProductImageController extends Controller
{
    //
    public function update(Request $request){

        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $sourcePath = $image->getPathName(); //imagente temporary location therum....source path nu pakaram


        $productImage = new ProductImage();
        $productImage->product_id = $request->product_id;
        $productImage->image = 'NULL';
        $productImage->save();

        $imageName = $request->product_id.'-'.$productImage->id.'-'.time().'.'.$ext;
        //product_id=>4; product_image_id => 1
        //name: 4-1-12345.jpg   Now this will become unique

        $productImage->image = $imageName;
        $productImage->save();

        // $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
        $destPath = public_path().'/uploads/product/large/'.$imageName;
        $image = Image::make($sourcePath);
        $image->resize(1400, null, function ($constraint) {
            $constraint->aspectRatio();
        }); //to maintain image aspect ratio
        $image->save($destPath);


        //Small image
        
        $destPath = public_path().'/uploads/product/small/'.$imageName;
        $image = Image::make($sourcePath);
        $image->fit(300, 300); //for fixed thumbnails
        $image->save($destPath);

        return response()->json([
            'status' => true,
            'image_id' => $productImage->id,
            'imagePath' => asset('uploads/product/small/'.$productImage->image),
            'message' => 'Image saved successfully'
        ]);

    }

    public function destroy(Request $request){
        $productImage = ProductImage::find($request->id);

        if(empty($productImage)){
             return response()->json([
                'status' => false,
                
                'message' => 'Image Not Found '
            ]);
        }

        //Delete images from folder
        File::delete(public_path('uploads/product/large/'.$productImage->image));
        File::delete(public_path('uploads/product/small/'.$productImage->image));

        $productImage->delete();
        return response()->json([
            'status' => true,
            
            'message' => 'Image deleted successfully'
        ]);
    }
}
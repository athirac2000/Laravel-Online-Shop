<?php

namespace App\Http\Controllers\admin;


use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Intervention\Image\Image;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;

class CategoryController extends Controller
{
    //
    public function index(Request $request){
        $categories = Category::latest();

        if(!empty($request->get('keyword'))){
            $categories = $categories->where('name','like','%'.$request->get('keyword').'%');
        }

        $categories = $categories->paginate(10);
        
        return view('admin.category.list',compact('categories'));
        
    }
    public function create(){
        return view('admin.category.create');
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);

        if($validator->passes()){

           $category = new Category();
           $category->name = $request->name;
           $category->slug = $request->slug;
           $category->status = $request->status;
           $category->showHome = $request->showHome;
           $category->save();

           //save image here

           if(!empty($request->image_id)){
           

                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);
                $newImageName = $category->id.'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/uploads/category/'.$newImageName; //store the image name as the id of category                File::copy($sPath,$dPath);
                File::copy($sPath, $dPath);
                


                //Generate Image thumbnails
                // $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
                 
                // $img = Image::make($sPath);
                // $img->resize(450, 600);
                // $img->save($dPath);

                $category->image = $newImageName;
                $category->save();
           }

           $request->session()->flash('success','Category added successfully');

           return response()->json([
            'status' => true,
            'message' => 'Category added successfully',
           ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function edit($categoryId, Request $request){
        $category = Category::find($categoryId);
        if(empty($category)){
            return redirect()->route('categories.index');
        }
        return view('admin.category.edit',compact('category'));
    }

    public function update($categoryId, Request $request){

        $category = Category::find($categoryId);
        if(empty($category)){
            $request->session()->flash('error','Category not found');

            return response()->json([
                'status'=> false,
                'notFound' => true,
                'message' => 'Category not found'
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->id.',id',
        ]);

        // 'unique:categories,slug,' . $category->id . ',id': This part of the rule checks for the uniqueness of the 'slug' within the 'categories' table. The format is unique:table,column,except,idColumn. Here's the breakdown:

        //     unique:categories,slug: This checks for the uniqueness of the 'slug' in the 'categories' table.
            
        //     ,' . $category->id . ',id': This part excludes the current record (identified by $category->id) from the uniqueness check. It is used when you are updating a record and want to make sure the 'slug' is unique among other records, excluding the current one.

        if($validator->passes()){

          
           $category->name = $request->name;
           $category->slug = $request->slug;
           $category->status = $request->status;
           $category->showHome = $request->showHome;

           $category->save();

           $oldImage = $category->image;

           //save image here

           if(!empty($request->image_id)){
           

                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'-'.time().'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/uploads/category/'.$newImageName; //store the image name as the id of category                File::copy($sPath,$dPath);
                File::copy($sPath, $dPath);
                


                //Generate Image thumbnails
                // $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
                 
                // $img = Image::make($sPath);
                // $img->resize(450, 600);
                // $img->save($dPath);

                $category->image = $newImageName;
                $category->save();

                //Delete old images here

                // File::delete(public_path().'/uploads/category/thumb/'.$oldImage);
                File::delete(public_path().'/uploads/category/'.$oldImage);
           }

           $request->session()->flash('success','Category updated successfully');

           return response()->json([
            'status' => true,
            'message' => 'Category updated successfully',
           ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function destroy($categoryId, Request $request){
        $category = Category::find($categoryId);
        if(empty($category)){
            $request->session()->flash('error','Category not found');

            return response()->json([
                'status' => true,
                'message' => 'Category deleted succesfully'
            ]);
            //return redirect()->route('categories.index');
        }

        
        // File::delete(public_path().'/uploads/category/thumb/'.$category->image);
        File::delete(public_path().'/uploads/category/'.$category->image);
        $category->delete();

        $request->session()->flash('success','Category deleted succesfully');

        return response()->json([
            'status' => true,
            'message' => 'Category deleted succesfully'
        ]);
    }
}

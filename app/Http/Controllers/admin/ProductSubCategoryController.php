<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\admin\SubCategory;
use App\Http\Controllers\Controller;

class ProductSubCategoryController extends Controller
{
    //
    public function index(Request $request){
        //categories ne base cheydtulla subcategories frontend il verneam , athond ayrkm ingne cheydekune
        
        if(!empty($request->category_id)){
            $subCategories = SubCategory::where('category_id',$request->category_id)
            ->orderBy('name','ASC')
            ->get();

            return response()->json([
                'status' => true,
                'subCategories' => $subCategories
            ]);
        } else {
            return response()->json([
                'status' =>true,
                'subCategories' => []
            ]);
        }

        
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\admin\SubCategory;

class ShopController extends Controller
{
    //slugs initailizing to null
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null){
        $categorySelected = '';
        $subCategorySelected = '';
        $brandsArray = [];

        $categories = Category::orderBy('name','ASC')->with('sub_category')->where('status',1)->get();
        $brands = Brand::orderBy('name','ASC')->where('status',1)->get();

        $products = Product::where('status',1);

        //Apply filters here
        if(!empty($categorySlug)){   //category filter
            $category = Category::where('slug',$categorySlug)->first();
            $products = $products->where('category_id',$category->id);
            $categorySelected = $category->id;
        }
        if(!empty($subCategorySlug)){   //sub category filter
            $subCategory = SubCategory::where('slug',$subCategorySlug)->first();
            $products = $products->where('sub_category_id',$subCategory->id);
            $subCategorySelected = $subCategory->id;

        }
        if(!empty($request->get('brand'))){ //In Laravel, a query string refers to the portion of a URL that comes after the "?" character and contains key-value pairs separated by "&" symbols. These key-value pairs are used to send data to the server as part of an HTTP request, typically for filtering, searching, or providing additional parameters to a request.
            $brandsArray = explode(',',$request->get('brand'));
            $products = $products->whereIn('brand_id',$brandsArray);
             

        }
        if($request->get('price_max') != '' && $request->get('price_min') != ''){

            if($request->get('price_max') == 1000){
                // Here intval() method will convert any string into numeric value, now its in query string format
                $products = $products->whereBetween('price',[intval($request->get('price_min')),1000000]);
            } else {
                $products = $products->whereBetween('price',[intval($request->get('price_min')),intval($request->get('price_max'))]); //1000 nu thazhe olla range anengil

            }
            
        }

        if($request->get('sort') != ''){
            if($request->get('sort') == 'latest'){
                $products = $products->orderBy('id','DESC'); //for latest products you can also use created_at column in orderBy() function
            } else if($request->get('sort') == 'price_asc'){
                $products = $products->orderBy('price','ASC'); 
            } else {
                $products = $products->orderBy('price','DESC');
            }

        } else {
            $products = $products->orderBy('id','DESC'); 

        }

        $products = $products->paginate(6);

        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['products'] = $products;
        $data['categorySelected'] = $categorySelected;
        $data['subCategorySelected'] = $subCategorySelected;
        $data['brandsArray'] = $brandsArray;
        $data['priceMax'] = (intval($request->get('price_max')) == 0) ? 1000 : $request->get('price_max');
        $data['priceMin'] = intval($request->get('price_min'));
        $data['sort'] = $request->get('sort');
        
        return view('front.shop',$data);
    }

    public function product($slug){
        $product = Product::where('slug',$slug)->with('product_images')->first();
        if($product == null){
            abort(404); //wrong slug vannal error page kanikan
        }

        $relatedProducts = [];
        //fetch related products
        if($product->related_products != ''){
            $productArray = explode(',',$product->related_products);
            $relatedProducts = Product::whereIn('id',$productArray)->with('product_images')->get();
        }

        $data['product'] = $product;
        $data['relatedProducts'] = $relatedProducts;


        return view('front.product', $data);
    }
}

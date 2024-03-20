<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart; //import cart library

class CartController extends Controller
{
    public function addToCart(Request $request){
        $product = Product::with('product_images')->find($request->id);
        if($product == null){
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ]);
        }

        if(Cart::count() > 0){
            // echo "Product already in cart";

            //Products found in cart
            //Check if this product already in the cart
            //Return a message that product already adde in your cart
            //If product not found in the cart then add product in cart

            $cartContent = Cart::content();
            $productAlreadyExist = false;

            foreach($cartContent as $item){
                if($item->id == $product->id){
                    $productAlreadyExist = true;
                }
            }

            if($productAlreadyExist == false){
                Cart::add($product->id,$product->title,1,$product->price,['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '' ]); //inbuilt library to add to cart, additional fields are passed as an array
                $status = true;
                $message = $product->title.' added in cart';

            } else {
                $status = false;
                $message = $product->title.' already added in cart';
            }

        } else {
            Cart::add($product->id,$product->title,1,$product->price,['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '' ]); //inbuilt library to add to cart, additional fields are passed as an array
            $status = true;
            $message = $product->title.' added in cart';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
        

    }
    public function cart(){
        $cartContent = Cart::content();
        // dd($cartContent);
        $data['cartContent'] = $cartContent;
        return view('front.cart',$data);
    }
}

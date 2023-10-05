<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        try{
            $user_cart = Cart::where('user_id',Auth::user()->id)->get();
            return response()->json([
                'success' => true,
                'data' => $user_cart->makeHidden(['user', 'product']),
            ]);
        } catch (Exception $e){
            return response()->json([
                'success' => false,
                'message' => "something went wrong",
            ]);
        }  
    }

    public function show($id)
    {
        $cart = Cart::find($id);
 
        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'cart not found '
            ], 500);
        }
 
        return response()->json([
            'success' => true,
            'data' => $cart->toArray()
        ], 400);
    }
 
    public function store(Request $request)
    {
        $input = $request->all();
    
        $validator = Validator::make($input, [
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'validate_err'=> $validator->messages(),
            ]);
        }
        try{
            $cart = Cart::create($input);
            return response()->json([
                'success' => true,
                'message' => 'Product Added to Cart Successfully',
            ], 400);
       } catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something Went Wrong',
            ], 500);
       }
        
    }
 
    public function update(Request $request, $id)
    {
        $input = $request->all();
       
        $validator = Validator::make($input, [
            'user_id' => 'sometimes|required|exists:users,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'quantity' => 'sometimes|required|integer|min:1'

        ]);
        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'validate_err'=> $validator->messages(),
            ]);
        }

        $cart = Cart::find($id);
        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'cart not found'
            ], 500);
        }
        try{
            $updated = $cart->fill($request->all())->save();
            if ($updated)
            return response()->json([
                'success' => true,
                'message' => 'cart Updated Successfully'
            ]);
        } catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something Went Wrong',
            ], 500);
        }  
    }
 
    public function destroy($id)
    {
        $cart = Cart::find($id);
 
        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'data not found'
            ], 400);
        }
 
        if ($cart->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart deleted successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'cart can not be deleted'
            ], 500);
        }
    }
}

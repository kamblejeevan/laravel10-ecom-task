<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        try{
            $products = Product::all();
            return response()->json([
                'success' => true,
                'data' => $products
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
       $products = Product::find($id);
 
        if (!$products) {
            return response()->json([
                'success' => false,
                'message' => 'product not found '
            ], 500);
        }
 
        return response()->json([
            'success' => true,
            'data' => $products->toArray()
        ], 400);
    }
 
    public function store(Request $request)
    {
        $input = $request->all();
       
        $validator = Validator::make($input, [
            'name' => 'required',
            'price' => 'required|numeric'
        ]);
       
        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'validate_err'=> $validator->messages(),
            ]);
        }
        try {
            $product = Product::create($input);
            return response()->json([
                'success' => true,
                'message' => 'product Added Successfully',
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
            'name' => 'sometimes|required',
            'price' => 'sometimes|required|numeric'
        ]);
       
        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'validate_err'=> $validator->messages(),
            ]);
        }

        $product = Product::find($id);
 
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'product not found'
            ], 500);
        }
        try{
            $updated = $product->fill($request->all())->save();
            if ($updated)
            return response()->json([
                'success' => true,
                'message' => 'Product Updated Successfully'
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
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'product not found'
            ], 500);
        }
 
        if ($product->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'product deleted successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'product can not be deleted'
            ], 500);
        }
    }
}
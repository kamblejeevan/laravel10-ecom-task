<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        try{
            $user_wishlist = Wishlist::where('user_id',Auth::user()->id)->get();
            return response()->json([
                'success' => true,
                'data' => $user_wishlist->makeHidden(['user', 'product']),
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
       $wishlist = Wishlist::find($id);
 
        if (!$wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'wishlist not found '
            ], 500);
        }
 
        return response()->json([
            'success' => true,
            'data' => $wishlist->makeHidden(['user', 'product']),
        ], 400);
    }
 
    public function store(Request $request)
    {
        $input = $request->all();
       
        $validator = Validator::make($input, [
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',

        ]);
        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'validate_err'=> $validator->messages(),
            ]);
        }
        try{
            $wishlist = Wishlist::create($input);
            return response()->json([
                'success' => true,
                'message' => 'Product Added to Wishlist Successfully',
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

        ]);
        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'validate_err'=> $validator->messages(),
            ]);
        }

        $wishlist = Wishlist::find($id);
        if (!$wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'wishlist not found'
            ], 500);
        }
        try{
            $updated = $wishlist->fill($request->all())->save();
            if ($updated)
            return response()->json([
                'success' => true,
                'message' => 'Wishlist Updated Successfully'
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
        $wishlist = Wishlist::find($id);
 
        if (!$wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'data not found'
            ], 400);
        }
 
        if ($wishlist->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Wishlist deleted successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Wishlist can not be deleted'
            ], 500);
        }
    }
}

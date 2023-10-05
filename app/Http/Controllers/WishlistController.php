<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * The index function retrieves the user's wishlist and returns it as a JSON response, excluding
     * the user and product details.
     * 
     * @author Jeevan
     * 
     * @return a JSON response. If the try block is successful, it will return a JSON object with the
     * keys 'success' set to true and 'data' containing the user's wishlist. The 'data' key will have
     * the 'user' and 'product' attributes hidden. If an exception occurs, it will return a JSON object
     * with the keys 'success' set to false and '
     */
    public function index() {
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

    /**
     * The function retrieves a wishlist by its ID and returns a JSON response with the wishlist data,
     * excluding the user and product information.
     * 
     * @author Jeevan
     * 
     * @param id The parameter "id" is the identifier of the wishlist that we want to retrieve and
     * show. It is used to find the wishlist record in the database.
     * 
     * @return a JSON response. If the wishlist is found, it will return a success response with the
     * wishlist data. If the wishlist is not found, it will return an error response.
     */
    public function show($id) {
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
 
    /**
     * The function stores a wishlist item in the database and returns a JSON response indicating
     * success or failure.
     * 
     * @author Jeevan
     * 
     * @param Request request The  parameter is an instance of the Request class, which
     * represents an HTTP request. It contains all the data and information about the incoming request,
     * such as the request method, headers, and request payload.
     * 
     * @return a JSON response. If the validation fails, it returns a JSON response with a "Validation
     * Error" message and the validation errors. If the creation of the wishlist is successful, it
     * returns a JSON response with a "success" message and a "Product Added to Wishlist Successfully"
     * message. If there is an exception thrown during the creation of the wishlist, it returns a JSON
     * response with
     */
    public function store(Request $request) {
        $input = $request->all();
       
        $validator = Validator::make($input, [
            'product_id' => 'required|exists:products,id',

        ]);
        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'validate_err'=> $validator->messages(),
            ]);
        }
        try{
            $request->request->add(['user_id' => Auth::user()->id]);
            $wishlist = Wishlist::create($request->all());
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
 
    /**
     * The function destroys a wishlist item by finding it based on the provided ID, and returns a JSON
     * response indicating success or failure.
     * 
     * @author Jeevan
     * 
     * @param id The parameter `` represents the ID of the wishlist item that needs to be deleted.
     * 
     * @return a JSON response. If the wishlist is found and successfully deleted, it will return a
     * success message with a status code of 400. If the wishlist is not found, it will return a
     * failure message with a status code of 400. If there is an error while deleting the wishlist, it
     * will return a failure message with a status code of 500.
     */
    public function destroy($id) {
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
                'message' => 'Removed Product from the Wishlist'
            ], 400);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Wishlist can not be deleted'
            ], 500);
        }
    }
}

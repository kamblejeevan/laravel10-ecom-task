<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    
   /**
    * The index function retrieves the user's cart items and returns a JSON response with the cart
    * data, excluding the user and product details, or an error message if something goes wrong.
    * 
    * @author Jeevan
    * 
    * @return a JSON response. If the try block is successful, it will return a JSON object with a
    * 'success' key set to true and a 'data' key containing the user's cart items. The 'user' and
    * 'product' attributes of each cart item are hidden in the response. If an exception occurs, it
    * will return a JSON object with a 'success' key set
    */
    public function index() {
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
   
   /**
    * The function retrieves a cart by its ID and returns a JSON response with the cart data if found,
    * or an error message if not found.
     * 
    * @author Jeevan
    * 
    * @param id The parameter "id" is used to identify the specific cart that needs to be shown. It is
    * passed as an argument to the "show" function. The function retrieves the cart with the given id
    * using the Cart model's "find" method. If the cart is not found, it returns a
    * 
    * @return a JSON response. If the cart is found, it will return a success response with the cart
    * data. If the cart is not found, it will return an error response with a message indicating that
    * the cart was not found.
    */
    public function show($id) {
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
 
    /**
     * The function is used to store a product in the cart and returns a JSON response indicating
     * success or failure.
     *
     * @author Jeevan
     * 
     * @param Request request The  parameter is an instance of the Request class, which
     * represents an HTTP request. It contains all the data and information about the request, such as
     * the request method, headers, and input data.
     * 
     * @return a JSON response. If the validation fails, it returns a JSON response with a "Validation
     * Error" message and the validation errors. If the creation of the cart is successful, it returns
     * a JSON response with a "success" message and a "Product Added to Cart Successfully" message. If
     * an exception occurs, it returns a JSON response with a "success" message and a "
     */
    public function store(Request $request) {
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
 
    /**
     * The above function is a PHP code snippet that updates a cart item based on the provided request
     * data and validates the input before updating.
     * 
     * @author Jeevan
     * 
     * @param Request request The  parameter is an instance of the Request class, which
     * represents an HTTP request. It contains all the data and information about the request, such as
     * the request method, headers, and input data.
     * @param id The  parameter in the update function represents the ID of the cart that needs to
     * be updated. It is used to find the specific cart record in the database and update its values.
     * 
     * @return a JSON response. If the validation fails, it returns a JSON response with a "Validation
     * Error" message and the validation errors. If the cart is not found, it returns a JSON response
     * with a "cart not found" message. If the cart is successfully updated, it returns a JSON response
     * with a "cart Updated Successfully" message. If there is an exception or error during
     */
    public function update(Request $request, $id) {
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
                'message' => 'Cart not found'
            ], 500);
        }
        try{
            $updated = $cart->fill($request->all())->save();
            if ($updated)
            return response()->json([
                'success' => true,
                'message' => 'Cart Updated Successfully'
            ]);
        } catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something Went Wrong',
            ], 500);
        }  
    }
 
   /**
    * The function "destroy" deletes a cart record and returns a JSON response indicating the success
    * or failure of the operation.
    * 
    * @author Jeevan
    *
    * @param id The parameter "id" is the identifier of the cart that needs to be deleted. It is used
    * to find the specific cart record in the database.
    * 
    * @return a JSON response. If the cart is found and successfully deleted, it will return a JSON
    * response with a success message. If the cart is not found, it will return a JSON response with a
    * message indicating that the data was not found. If there is an error while deleting the cart, it
    * will return a JSON response with a message indicating that the cart cannot be deleted.
    */
    public function destroy($id) {
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
                'message' => 'Product Removed from the Cart'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'cart can not be deleted'
            ], 500);
        }
    }
}

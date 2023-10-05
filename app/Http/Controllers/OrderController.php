<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * The index function retrieves orders with their payment and order items for the authenticated
     * user and returns a JSON response with the data.
     * 
     * @author Jeevan
     * 
     * @return a JSON response. If the try block is successful, it will return a JSON response with a
     * 'success' key set to true, and a 'data' key containing the orders. The 'user' attribute of each
     * order will be hidden in the response. If there is an exception caught in the try block, it will
     * return a JSON response with a 'success' key set
     */
    public function index() {
        try{
            $orders = Order::with('payment','orderItems')->where('user_id',Auth::user()->id)->get();
            return response()->json([
                'success' => true,
                'data' => $orders->makeHidden(['user']),
            ], 400);
        } catch (Exception $e   ){
            return response()->json([
                'success' => false,
                'message' => "something went wrong",
            ], 500);
        }  
    }

   /**
    * The function retrieves an order by its ID and returns a JSON response with the order data if
    * found, or an error message if not found.
    * 
    * @author Jeevan
    * 
    * @param id The parameter "id" is the identifier of the order that we want to retrieve and display.
    * It is used to find the order in the database using the "find" method of the "Order" model.
    * 
    * @return a JSON response. If the order is found, it will return a success response with the order
    * data. If the order is not found, it will return an error response with a message indicating that
    * the order was not found.
    */
    public function show($id) {
       $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'order not found '
            ], 500);
        }
 
        return response()->json([
            'success' => true,
            'data' => $order->toArray()
        ], 400);
    }

   /**
    * The function "store" in PHP creates an order with validation, calculates the total amount based
    * on the products in the cart, and saves the order items.
    * 
    * @author Jeevan
    * 
    * @param Request request The  parameter is an instance of the Request class, which
    * represents an HTTP request. It contains all the data and information about the request, such as
    * the request method, headers, and input data.
    * 
    * @return a JSON response. If the validation fails, it returns a JSON response with a "Validation
    * Error" message and the validation errors. If the cart is empty, it returns a JSON response with a
    * "Cart is empty include atleast one item" message. If the order is created successfully, it
    * returns a JSON response with a "Order Created Successfully" message. If something goes wrong
    * during
    */
    public function store(Request $request) {
        $data_arr = ['user_id'=>Auth::user()->id,'status' => 3];
        try{
            $order = Order::create($data_arr);
            $carts = Cart::where('user_id',Auth::user()->id)->get();
            if(!$carts){
                return response()->json([
                    'success' => true,
                    'message' => 'Cart is empty include atleast one item',
                ], 500);
            }
            $amount = [];
            foreach($carts as $cart){
                $product = Product::find($cart->product_id);
                $order_items =  new OrderItem;
                $order_items->order_id = $order->id;
                $order_items->product_id = $cart->product_id;
                $order_items->price = $product->price;
                $order_items->quantity = $cart->quantity;
                $amount[$cart->product_id] = $product->price * $cart->quantity;
                $order_items->save();
            }
            Order::where('id', $order->id)
                ->update([
                    'amount' => array_sum($amount),
                    'order_date' => Carbon::now(),
                ]);
            return response()->json([
                'success' => true,
                'message' => 'Order Created Successfully',
            ], 400);
       } catch(Exception $e){
            return response()->json([
                'success' => true,
                'message' => 'Something Went Wrong',
            ], 500);
       }  
    }

    /**
     * The function "destroy" deletes an order with the given ID and returns a JSON response indicating
     * the success or failure of the deletion.
     * 
     * @author Jeevan
     * 
     * @param id The parameter "id" is the identifier of the order that needs to be deleted. It is used
     * to find the specific order in the database and delete it.
     * 
     * @return a JSON response. If the order is found and successfully deleted, it will return a JSON
     * response with 'success' set to true and 'message' set to 'Order Deleted Successfully'. If the
     * order is not found, it will return a JSON response with 'success' set to false and 'message' set
     * to 'data not found'. If the order cannot be deleted for some
     */
    public function destroy($id) {
        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'data not found'
            ], 400);
        }
 
        if ($order->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Order Deleted Successfully'
            ],400);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'order can not be deleted'
            ], 500);
        }
    }
}

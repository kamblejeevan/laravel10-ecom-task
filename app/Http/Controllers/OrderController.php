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
    public function index()
    {
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

    public function show($id)
    {
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

    public function store(Request $request)
    {
        $input = $request->all();
       
        $validator = Validator::make($input, [
            'user_id' => 'required|exists:users,id',
        ]);
        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'validate_err'=> $validator->messages(),
            ]);
        }
        $request->request->add(['status' => 3]);
        try{
            $order = Order::create($request->all());
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

    public function destroy($id)
    {
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
                'message' => 'Order Deleted Successfully'
            ], 500);
        }
    }
}

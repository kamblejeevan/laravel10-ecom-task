<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
Use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    /**
     * The function fetches payment details based on the provided ID and returns a JSON response with
     * the payment data or an error message.
     * 
     * @author Jeevan
     * 
     * @param id The parameter "id" is the identifier of the payment that you want to fetch the details
     * for. It is used to find the payment record in the database.
     * 
     * @return a JSON response. If the payment is found successfully, it will return a JSON object with
     * the 'success' key set to true and the 'data' key set to the payment details. If an exception
     * occurs, it will return a JSON object with the 'success' key set to true and the 'message' key
     * set to "something went wrong".
     */
    public function fetch_payment_details($id) {
        try{
            $payment = Payment::find($id);
            return response()->json([
                'success' => true,
                'data' => $payment,
            ]);
        } catch (Exception $e){
            return response()->json([
                'success' => true,
                'message' => "something went wrong",
            ]);
        }  
    }

    /**
     * The function `place_order` is responsible for validating the request data, creating a payment
     * intent, confirming the payment, and updating the order and payment records accordingly.
     * 
     * @author Jeevan
     * 
     * @param Request request The `` parameter is an instance of the `Illuminate\Http\Request`
     * class. It represents the HTTP request made to the server and contains all the data and
     * information sent with the request.
     * 
     * @return a JSON response. If the order is not found, it returns a JSON response with a success
     * status of false and a message indicating that the order was not found. If there is a failure in
     * creating the payment intent or confirming the payment, it returns a JSON response with an error
     * message. If the payment is successfully initiated, it returns a JSON response with a message
     * indicating that the
     */
    public function place_order(Request $request) {
        
        $request->validate([
            'order_id' => 'required|integer'
        ]);

        $order = Order::find($request->order_id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'order not found '
            ], 400);
        }
        try{
            $paymentIntentResponse = Http::post('https://fakedata.nanocorp.io/api/payment/create', [
                'order_id' => $request->order_id,
                'customer_email' => Auth::user()->email,
                'amount' => $order->amount,
            ]);

            $response_data = $paymentIntentResponse->json();
           
            if ($response_data["result"] == "failure") {
                return response()->json(['error' => 'Failed to create payment intend'], 500);
            }
          
            $payment_intende = $response_data['data']['payment_intend'];
            // Complete the order by confirming the payment
            $confirm_payment_response = Http::post('https://fakedata.nanocorp.io/api/payment/confirm', [
                'payment_intend' => $payment_intende,
            ]);
            $confirm_payment_response_json = $confirm_payment_response->json();
            if ($confirm_payment_response_json["result"] == "failure") {
                $data = [
                    'payment_intend' => $payment_intende, 
                    'amount' => $order->amount, 
                    'customer_email' => Auth::user()->email,
                    'payment_at' => null,
                    'intiated_at' => null,
                    'status' => 2
                ];
                
                $payment = Payment::create($data);
                return response()->json(['error' => 'Payment Failed'], 500);
            }
           
            $payment_resposne_data = $confirm_payment_response_json['data']['payment_intend'];
         
            $data = [
                'payment_intend' => $payment_intende, 
                'amount' => $order->amount, 
                'customer_email' => Auth::user()->email,
                'payment_at' => Carbon::parse($payment_resposne_data['payment_at'])->format('Y-m-d H:i:s.uZ'),
                'intiated_at' => Carbon::parse($payment_resposne_data['intiated_at'])->format('Y-m-d H:i:s.uZ'),
                'status' => 1
            ];
    
            $payment = Payment::create($data);
            Order::where('id', $request->order_id)
                ->update([
                    'payment_id' => $payment->id,
                    'status' => 1,
                ]);
            return response()->json(['message' => 'Payment Initated successfully']);
        } catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Sorry to inform you, we are facing some technical issues. Please try again sometime.'
            ], 500);
        }
       
    }
}

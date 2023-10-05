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
    public function fetch_payment_details($id)
    {
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

    public function place_order(Request $request)
    {
        // Validate the request data
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

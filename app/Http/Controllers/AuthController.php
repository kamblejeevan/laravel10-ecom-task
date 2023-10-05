<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
  
   /**
    *  creates a new user with the provided information, and returns a token for authentication.
    * 
    * @author Jeevan
    * 
    * @param Request request The request object.
    * 
    */
    public function register(Request $request)
    {
        $input = $request->all();
       
        $validator = Validator::make($input, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'phone_no' => 'required',
            'password' => 'required'
        ]);
       
        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'validate_err'=> $validator->messages(),
            ]);
        }
 
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
            'password' => bcrypt($request->password)
        ]);
       
        $token = $user->createToken('LaravelAuthApp')->accessToken;
 
        return response()->json(['message'=> 'User Created Successfully','token' => $token], 200);
    }
 
    /**
     * The function attempts to log in a user by checking their email and password, and if successful,
     * it generates an OAuth token and returns it as a JSON response.
     * 
     * @author Jeevan
     * 
     * @param Request request The request object.
     * 
    */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($data)) {
            $baseUrl = url('/');
            $request = Request::create($baseUrl.'/oauth/token', 'POST', [
                'grant_type' => 'password',
                'username' => $request->email,
                'password' => $request->password,
                'client_id' => env('CLIENT_ID'),
                'client_secret' => env('CLIENT_SECRET'),
                "scope" =>  ""
            ]);
            
            $result = app()->handle($request);
            $response = json_decode($result->getContent(), true); 
            return response()->json($response, 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }  
}
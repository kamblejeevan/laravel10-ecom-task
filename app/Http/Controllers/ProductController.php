<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * The index function retrieves all products and returns a JSON response with the success status
     * and the data. If an exception occurs, it returns a JSON response with the success status set to
     * false and an error message.
     * 
     * @author Jeevan
     * 
     * @return a JSON response. If the try block is successful, it will return a JSON object with a
     * 'success' key set to true and a 'data' key containing the products. If an exception is caught in
     * the catch block, it will return a JSON object with a 'success' key set to false and a 'message'
     * key containing the error message "something went wrong".
     */
    public function index() {
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

    /**
     * The function retrieves a product with a given ID and returns a JSON response with the product
     * data if found, or an error message if not found.
     * 
     * @author Jeevan
     * 
     * @param id The "id" parameter is the unique identifier of the product that we want to retrieve
     * and display. It is used to find the product in the database using the "find" method of the
     * "Product" model.
     * 
     * @return a JSON response. If the product is found, it will return a success response with the
     * product data. If the product is not found, it will return an error response with a message
     * indicating that the product was not found.
     */
    public function show($id) {
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
 
    /**
     * The above function is a PHP code snippet that updates a product based on the input received from
     * a request, performs validation on the input fields, and returns a JSON response indicating the
     * success or failure of the update operation.
     * 
     * @author Jeevan
     * 
     * @param Request request The  parameter is an instance of the Request class, which
     * represents an HTTP request. It contains all the data and information about the request, such as
     * the request method, headers, and input data.
     * @param id The  parameter is the identifier of the product that needs to be updated. It is
     * used to find the specific product in the database and update its details.
     * 
     * @return a JSON response. If the validation fails, it returns a JSON response with a "Validation
     * Error" message and the validation errors. If the product is not found, it returns a JSON
     * response with a "product not found" message. If the product is successfully updated, it returns
     * a JSON response with a "Product Updated Successfully" message. If an exception occurs during the
     * update process
     */
    public function update(Request $request, $id) {
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
 
    /**
     * The function "destroy" deletes a product with the given ID and returns a JSON response
     * indicating whether the deletion was successful or not.
     * 
     * @author Jeevan
     * 
     * @param id The "id" parameter is the unique identifier of the product that needs to be deleted.
     * 
     * @return a JSON response. If the product is found and successfully deleted, it will return a JSON
     * response with a success message. If the product is not found, it will return a JSON response
     * with a failure message indicating that the product was not found. If the product cannot be
     * deleted for some reason, it will return a JSON response with a failure message indicating that
     * the product cannot be deleted
     */
    public function destroy($id) {
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
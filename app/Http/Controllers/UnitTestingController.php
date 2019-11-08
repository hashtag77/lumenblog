<?php

namespace App\Http\Controllers;

use App\User;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitTestingController extends Controller
{
    public function testUserCreation(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        try {
            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);
            $user->save();
            
            return response()->json(['message' => 'Created!'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }
    }

    public function testUserLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function testUserProfile()
    {
        $user = Auth::user();
        if($user) {
            return response()->json(['message' =>  'Works!'], 200);
        } else {
            return response()->json(['message' =>  'Something went wrong!'], 200);
        }
    }

    public function testUserAll()
    {
        $user = User::all();
        if($user) {
            return response()->json(['message' =>  'Fetched!'], 200);
        } else {
            return response()->json(['message' =>  'Something went wrong!'], 200);
        }
    }

    public function testUserData($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json(['message' => 'User!'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User not found!'], 404);
        }
    }

    public function testAllProduct()
    {
        $products = Product::orderBy('updated_at', 'desc')->paginate(10);

        if ($products) {
            return response()->json(['message' => 'All fetched!'], 201);
        } else {
            return response()->json(['message' => 'Nothing found!'], 201);
        }
    }
  
    public function testCreateProduct(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'detail' => 'required',
        ]);

        try {
            $product = Product::create($request->all());

            return response()->json(['message' => 'Created!'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!'], 409);
        }
    }

    public function testShowProduct($id)
    {
        $product = Product::find($id);
        
        if ($product) {
            return response()->json(['message' => 'Fetched!'], 201);
        } else {
            return response()->json(['message' => 'Item not found!'], 409);
        }
    }
  
    public function testUpdateProduct(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'detail' => 'required',
        ]);

        try {
            $product = Product::find($id);
            if ($product) {
                $product->update($request->all());

                return response()->json(['message' => 'Updated!'], 201);
            } else {
                return response()->json(['message' => 'Item not found!'], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!'], 409);
        }
    }
  
    public function testDeleteProduct($id)
    {
        $product = Product::find($id);

        if ($product) {
            $product->delete();
            
            return response()->json(['message' => 'Item deleted!'], 201);
        } else {
            return response()->json(['message' => 'Item not found!'], 409);
        }
    }
}
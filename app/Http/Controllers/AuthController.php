<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies',
            'nif' => 'required|string|max:255|unique:companies',
            'phone' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
                'data' => null
            ], 422);
        }

        $company = Company::create([
            'name' => $request->name,
            'email' => $request->email,
            'nif' => $request->nif,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Company criada com successo',
            'data' => $company
        ], 201);

        //return response()->json(['message' => 'Company registered successfully'], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('company')->attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Email ou password inválida!',
                'data' => null
            ], 401);
        }

        $company = Auth::guard('company')->user();
        return response()->json([
            'status' => true,
            'message' => 'Login realizado com sucesso!',
            'auth_data' => $this->respondWithToken($token)->original,
            'data' =>  $company
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Implementar lógica de recuperação de senha aqui

        return response()->json(['message' => 'Password reset link sent'], 200);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('company')->factory()->getTTL() * 60
        ]);
    }
}

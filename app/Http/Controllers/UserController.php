<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $companyId = Auth::guard('company')->user()->id;
        $users = User::where('company_id', $companyId)->get();
        return response()->json([
            'status' => true,
            'message' => 'Usuários listados com sucesso!',
            'data' => $users
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => Auth::guard('company')->user()->id,
        ]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => Auth::guard('company')->user()->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Usuário criado com sucesso!',
            'data' => $user
        ], 201);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user || $user->company_id != Auth::guard('company')->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Usuário não encontrado!',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Usuário encontrado com sucesso!',
            'data' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user || $user->company_id != Auth::guard('company')->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Usuário não encontrado!',
                'data' => null
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user->update($request->only('name', 'email', 'password'));

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Usuário atualizado com sucesso!',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user || $user->company_id != Auth::guard('company')->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Usuário não encontrado!',
                'data' => null
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'Usuário apagado com sucesso!',
            'data' => null
        ]);
    }
}

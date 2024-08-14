<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        $companyId = Auth::guard('company')->user()->id;
        $clients = Customer::where('company_id', $companyId)->get();
        return response()->json([
            'status' => true,
            'message' => 'Clientes listados com sucesso!',
            'data' => $clients
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'phone' => 'required|string|max:255',
            'nif' => 'required|string|max:255|unique:clients',
            'address' => 'required|string',
            'country_id' => 'required|exists:countries,id',
            'status' => 'required|in:Ativo,Inativo',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $client = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'nif' => $request->nif,
            'address' => $request->address,
            'country_id' => $request->country_id,
            'status' => $request->status,
            'company_id' => Auth::guard('company')->user()->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cliente criado com sucesso!',
            'data' => $client
        ], 201);
    }

    public function show($id)
    {
        $client = Customer::find($id);

        if (!$client || $client->company_id != Auth::guard('company')->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Cliente não encontrado!',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Cliente encontrado com sucesso!',
            'data' => $client
        ]);
    }

    public function update(Request $request, $id)
    {
        $client = Customer::find($id);

        if (!$client || $client->company_id != Auth::guard('company')->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Cliente não encontrado!',
                'data' => null
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:clients,email,' . $id,
            'phone' => 'sometimes|required|string|max:255',
            'nif' => 'sometimes|required|string|max:255|unique:clients,nif,' . $id,
            'address' => 'sometimes|required|string',
            'country_id' => 'sometimes|required|exists:countries,id',
            'status' => 'sometimes|required|in:Ativo,Inativo',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $client->update($request->only('name', 'email', 'phone', 'nif', 'address', 'country_id', 'status'));

        return response()->json([
            'status' => true,
            'message' => 'Cliente atualizado com sucesso!',
            'data' => $client
        ]);
    }

    public function destroy($id)
    {
        $client = Customer::find($id);

        if (!$client || $client->company_id != Auth::guard('company')->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Cliente não encontrado!',
                'data' => null
            ], 404);
        }

        $client->delete();

        return response()->json([
            'status' => true,
            'message' => 'Cliente apagado com sucesso!',
            'data' => null
        ]);
    }
}
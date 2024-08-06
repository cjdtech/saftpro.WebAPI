<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    
    public function index()
    {
        $companyId = Auth::guard('company')->user()->id;
        $products = Product::where('company_id', $companyId)->get();
        return response()->json([
            'status' => true,
            'message' => 'Produtos listados com sucesso!',
            'data' => $products
        ]);
    }

    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer',
            'type' => 'required|string|max:255',
            'price' => 'required|numeric',
            'iva' => 'required|in:Isento,10%,15%',
            'status' => 'required|in:Ativo,Inativo',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'type' => $request->type,
            'price' => $request->price,
            'iva' => $request->iva,
            'status' => $request->status,
            'category_id' => $request->category_id,
            'company_id' => Auth::guard('company')->user()->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Produto criado com sucesso!',
            'data' => $product
        ], 201);
    }

    
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product || $product->company_id != Auth::guard('company')->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Produto não encontrado!',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Produto encontrado com sucesso!',
            'data' => $product
        ]);
    }

    
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product || $product->company_id != Auth::guard('company')->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Produto não encontrado!',
                'data' => null
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'sometimes|required|integer',
            'type' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'iva' => 'sometimes|required|in:Isento,10%,15%',
            'status' => 'sometimes|required|in:Ativo,Inativo',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $product->update($request->only('name', 'description', 'quantity', 'type', 'price', 'iva', 'status', 'category_id'));

        return response()->json([
            'status' => true,
            'message' => 'Produto atualizado com sucesso!',
            'data' => $product
        ]);
    }

    
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product || $product->company_id != Auth::guard('company')->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Produto não encontrado!',
                'data' => null
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Produto apagado com sucesso!',
            'data' => null
        ]);
    }
}

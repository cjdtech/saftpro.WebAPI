<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    
    public function index()
    {
        $companyId = Auth::guard('company')->user()->id;
        $categories = Category::where('company_id', $companyId)->get();
        return response()->json([
            'status' => true,
            'message' => 'Categorias listadas com sucesso!',
            'data' => $categories
        ]);
    }

    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'company_id' => Auth::guard('company')->user()->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Categoria criada com sucesso!',
            'data' => $category
        ], 201);
    }

    
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category || $category->company_id != Auth::guard('company')->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Categoria não encontrada!',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Categoria encontrada com sucesso!',
            'data' => $category
        ]);
    }

    
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category || $category->company_id != Auth::guard('company')->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Categoria não encontrada!',
                'data' => null
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category->update($request->only('name', 'description'));

        return response()->json([
            'status' => true,
            'message' => 'Categoria atualizada com sucesso!',
            'data' => $category
        ]);
    }

    
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category || $category->company_id != Auth::guard('company')->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Categoria não encontrada!',
                'data' => null
            ], 404);
        }

        $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Categoria apagada com sucesso!',
            'data' => null
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        return response()->json([
            'status' => true,
            'message' => 'Países listados com sucesso!',
            'data' => $countries
        ]);
    }
}

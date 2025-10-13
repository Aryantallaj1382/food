<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $categories = Category::select(['id','name','icon'])->get();
        return api_response([
            'slider' => null,
            'categories' => $categories,
        ]);
    }
}

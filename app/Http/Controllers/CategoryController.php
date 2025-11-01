<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('posts')->get();

        return $this->sendResponse([
            'categories' => CategoryResource::collection($categories),
        ], 'Categories retrieved successfully');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::all();
    }


    public function store(Request $request)
    {

        if (! $request->user()->isAdmin() && ! $request->user()->isSuperAdmin()) {
            abort(403, 'Only admins can create categories');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category ]);
    }
}

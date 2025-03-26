<?php

namespace App\Http\Controllers;

use App\Models\QuestionCategory;
use Illuminate\Http\Request;

class NewCategoryController extends Controller
{
    public function index()
    {
        // $categories = QuestionCategory::all();
        // return view('new-category.index', compact('categories'));
        $categories = QuestionCategory::orderBy('order_by', 'asc')->get();
        return view('new-category.index', compact('categories'));
    }

    public function create()
    {
        return view('new-category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_by' => 'nullable|integer',
            'status' => 'required',
        ]);

        QuestionCategory::create($request->all());

        return redirect()->route('new-categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(QuestionCategory $new_category)
    {
        return view('new-category.edit', ['category' => $new_category]);
    }

    public function update(Request $request, QuestionCategory $new_category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_by' => 'nullable|integer',
            'status' => 'required',
        ]);

        $new_category->update($request->all());

        return redirect()->route('new-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(QuestionCategory $new_category)
    {
        $new_category->delete();
        return redirect()->route('new-categories.index')->with('success', 'Category deleted successfully.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Database\QueryException; 

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Super Admin|Admin Gudang');
    }

    public function index(Request $request)
    {
        $q = Category::query();
        if ($s = trim($request->get('q', ''))) {
            $q->where('category_name', 'like', "%{$s}%");
        }

        $categories = $q->orderBy('category_name')->paginate(12)->withQueryString();

        return view('categories.index', [
            'categories' => $categories,
            'search'     => $s,
        ]);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $row = Category::create($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', "Kategori <strong>{$row->category_name}</strong> berhasil dibuat.");
    }

    public function edit(Category $category)
    {
        return view('categories.edit', ['category' => $category]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', "Kategori <strong>{$category->category_name}</strong> berhasil diperbarui.");
    }

    public function destroy(Category $category)
    {
        $name = $category->category_name;

        try {
            $category->delete();

            return redirect()
                ->route('categories.index')
                ->with('success', "Kategori <strong>{$name}</strong> berhasil dihapus.");
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()
                    ->route('categories.index')
                    ->with(
                        'error',
                        "Kategori <strong>{$name}</strong> tidak dapat dihapus karena masih dipakai ".
                        "oleh <strong>Item Master</strong>. " .
                        "Silakan pindahkan / hapus dulu item yang menggunakan kategori ini."
                    );
            }

            return redirect()
                ->route('categories.index')
                ->with('error', "Terjadi kesalahan saat menghapus kategori <strong>{$name}</strong>.");
        }
    }
}

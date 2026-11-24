<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * CRUD kategori kamar (staff & admin). Penanggung jawab: Fais.
 */
class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('roomTypes')->orderBy('name')->get();

        return view('dashboard.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('dashboard.categories.form', ['category' => new Category()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);

        $category = Category::create($data);
        ActivityLog::record('category.created', "Menambah kategori {$category->name}", $category);

        return redirect()->route('dashboard.categories.index')->with('status', "Kategori {$category->name} ditambahkan.");
    }

    public function edit(Category $category): View
    {
        return view('dashboard.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request, $category);
        if ($data['name'] !== $category->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $category->id);
        }

        $category->update($data);
        ActivityLog::record('category.updated', "Mengubah kategori {$category->name}", $category);

        return redirect()->route('dashboard.categories.index')->with('status', "Kategori {$category->name} diperbarui.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->roomTypes()->exists()) {
            return back()->with('info', "Kategori {$category->name} masih dipakai jenis kamar. Pindahkan atau hapus kamarnya dulu.");
        }

        $name = $category->name;
        ActivityLog::record('category.deleted', "Menghapus kategori {$name}", null, ['name' => $name]);
        $category->delete();

        return back()->with('status', "Kategori {$name} dihapus.");
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name'        => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category?->id)],
            'description' => ['nullable', 'string', 'max:500'],
        ], ['name.unique' => 'Nama kategori ini sudah ada.'], ['name' => 'nama kategori', 'description' => 'deskripsi']);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'kategori';
        $slug = $base;
        for ($i = 2; Category::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }
}

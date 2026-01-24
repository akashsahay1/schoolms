<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = BookCategory::withCount('books');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $categories = $query->orderBy('name')->paginate(15);
        $trashedCount = BookCategory::onlyTrashed()->count();

        return view('admin.library.categories.index', compact('categories', 'trashedCount'));
    }

    public function create()
    {
        return view('admin.library.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:book_categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            BookCategory::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.library.categories.index')
                ->with('success', 'Book category created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(BookCategory $category)
    {
        return view('admin.library.categories.edit', compact('category'));
    }

    public function update(Request $request, BookCategory $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:book_categories,name,' . $category->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            $category->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.library.categories.index')
                ->with('success', 'Book category updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(BookCategory $category)
    {
        try {
            // Check if category has books
            if ($category->books()->count() > 0) {
                return back()->with('error', 'Cannot delete category with associated books. Please reassign or delete the books first.');
            }

            $category->delete();

            return redirect()->route('admin.library.categories.index')
                ->with('success', 'Book category moved to trash successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['exists:book_categories,id'],
        ]);

        try {
            // Check for categories with books
            $categoriesWithBooks = BookCategory::whereIn('id', $request->category_ids)
                ->whereHas('books')
                ->count();

            if ($categoriesWithBooks > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete {$categoriesWithBooks} category(ies) with associated books.",
                ], 422);
            }

            $count = BookCategory::whereIn('id', $request->category_ids)->count();
            BookCategory::whereIn('id', $request->category_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$count} category(ies) moved to trash.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function trash(Request $request)
    {
        $query = BookCategory::onlyTrashed();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->latest('deleted_at')->paginate(15);
        $trashedCount = BookCategory::onlyTrashed()->count();

        return view('admin.library.categories.trash', compact('categories', 'trashedCount'));
    }

    public function restore($id)
    {
        try {
            $category = BookCategory::onlyTrashed()->findOrFail($id);
            $category->restore();

            return redirect()->route('admin.library.categories.trash')
                ->with('success', "Category '{$category->name}' restored successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $category = BookCategory::onlyTrashed()->findOrFail($id);
            $name = $category->name;
            $category->forceDelete();

            return redirect()->route('admin.library.categories.trash')
                ->with('success', "Category '{$name}' permanently deleted.");
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function bulkRestore(Request $request)
    {
        $request->validate([
            'category_ids' => ['required', 'array', 'min:1'],
        ]);

        try {
            $count = BookCategory::onlyTrashed()->whereIn('id', $request->category_ids)->count();
            BookCategory::onlyTrashed()->whereIn('id', $request->category_ids)->restore();

            return response()->json([
                'success' => true,
                'message' => "{$count} category(ies) restored successfully.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkForceDelete(Request $request)
    {
        $request->validate([
            'category_ids' => ['required', 'array', 'min:1'],
        ]);

        try {
            $count = BookCategory::onlyTrashed()->whereIn('id', $request->category_ids)->count();
            BookCategory::onlyTrashed()->whereIn('id', $request->category_ids)->forceDelete();

            return response()->json([
                'success' => true,
                'message' => "{$count} category(ies) permanently deleted.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function emptyTrash()
    {
        try {
            $count = BookCategory::onlyTrashed()->count();
            BookCategory::onlyTrashed()->forceDelete();

            return redirect()->route('admin.library.categories.trash')
                ->with('success', "{$count} category(ies) permanently deleted from trash.");
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}

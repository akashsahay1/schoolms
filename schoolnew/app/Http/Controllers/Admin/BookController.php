<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
	public function index(Request $request)
	{
		$query = Book::with('category');

		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('title', 'like', "%{$search}%")
					->orWhere('author', 'like', "%{$search}%")
					->orWhere('isbn', 'like', "%{$search}%");
			});
		}

		if ($request->filled('category')) {
			$query->where('book_category_id', $request->category);
		}

		if ($request->filled('status')) {
			if ($request->status === 'available') {
				$query->where('available_copies', '>', 0);
			} elseif ($request->status === 'unavailable') {
				$query->where('available_copies', 0);
			}
		}

		$books = $query->orderBy('title')->paginate(15);
		$categories = BookCategory::active()->orderBy('name')->get();
		$trashedCount = Book::onlyTrashed()->count();

		return view('admin.library.books.index', compact('books', 'categories', 'trashedCount'));
	}

	public function create()
	{
		$categories = BookCategory::active()->orderBy('name')->get();
		return view('admin.library.books.create', compact('categories'));
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'book_category_id' => ['required', 'exists:book_categories,id'],
			'title' => ['required', 'string', 'max:255'],
			'author' => ['required', 'string', 'max:255'],
			'isbn' => ['nullable', 'string', 'max:50', 'unique:books,isbn'],
			'publisher' => ['nullable', 'string', 'max:255'],
			'edition' => ['nullable', 'string', 'max:50'],
			'published_year' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
			'total_copies' => ['required', 'integer', 'min:1'],
			'price' => ['nullable', 'numeric', 'min:0'],
			'rack_no' => ['nullable', 'string', 'max:50'],
			'description' => ['nullable', 'string'],
			'cover_image' => ['nullable', 'image', 'max:2048'],
			'is_active' => ['nullable', 'boolean'],
		]);

		try {
			$coverPath = null;
			if ($request->hasFile('cover_image')) {
				$coverPath = $request->file('cover_image')->store('books', 'public');
			}

			Book::create([
				'book_category_id' => $validated['book_category_id'],
				'title' => $validated['title'],
				'author' => $validated['author'],
				'isbn' => $validated['isbn'] ?? null,
				'publisher' => $validated['publisher'] ?? null,
				'edition' => $validated['edition'] ?? null,
				'published_year' => $validated['published_year'] ?? null,
				'total_copies' => $validated['total_copies'],
				'available_copies' => $validated['total_copies'],
				'price' => $validated['price'] ?? 0,
				'rack_no' => $validated['rack_no'] ?? null,
				'description' => $validated['description'] ?? null,
				'cover_image' => $coverPath,
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.library.books.index')->with('success', 'Book added successfully.');
		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function edit(Book $book)
	{
		$categories = BookCategory::active()->orderBy('name')->get();
		return view('admin.library.books.edit', compact('book', 'categories'));
	}

	public function update(Request $request, Book $book)
	{
		$validated = $request->validate([
			'book_category_id' => ['required', 'exists:book_categories,id'],
			'title' => ['required', 'string', 'max:255'],
			'author' => ['required', 'string', 'max:255'],
			'isbn' => ['nullable', 'string', 'max:50', 'unique:books,isbn,' . $book->id],
			'publisher' => ['nullable', 'string', 'max:255'],
			'total_copies' => ['required', 'integer', 'min:1'],
			'price' => ['nullable', 'numeric', 'min:0'],
			'is_active' => ['nullable', 'boolean'],
		]);

		try {
			$book->update([
				'book_category_id' => $validated['book_category_id'],
				'title' => $validated['title'],
				'author' => $validated['author'],
				'isbn' => $validated['isbn'] ?? null,
				'publisher' => $validated['publisher'] ?? null,
				'total_copies' => $validated['total_copies'],
				'price' => $validated['price'] ?? 0,
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.library.books.index')->with('success', 'Book updated successfully.');
		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function destroy(Book $book)
	{
		try {
			if ($book->issues()->where('status', 'issued')->count() > 0) {
				return back()->with('error', 'Cannot delete book with active issues.');
			}

			$book->delete();

			return redirect()->route('admin.library.books.index')
				->with('success', 'Book moved to trash successfully.');
		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function bulkDelete(Request $request)
	{
		$request->validate([
			'book_ids' => ['required', 'array', 'min:1'],
			'book_ids.*' => ['exists:books,id'],
		]);

		try {
			// Check for active issues
			$booksWithActiveIssues = Book::whereIn('id', $request->book_ids)
				->whereHas('issues', function ($query) {
					$query->where('status', 'issued');
				})
				->count();

			if ($booksWithActiveIssues > 0) {
				return response()->json([
					'success' => false,
					'message' => "Cannot delete {$booksWithActiveIssues} book(s) with active issues.",
				], 422);
			}

			$count = Book::whereIn('id', $request->book_ids)->count();
			Book::whereIn('id', $request->book_ids)->delete();

			return response()->json([
				'success' => true,
				'message' => "{$count} book(s) moved to trash.",
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
		$query = Book::onlyTrashed()->with('category');

		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('title', 'like', "%{$search}%")
					->orWhere('author', 'like', "%{$search}%")
					->orWhere('isbn', 'like', "%{$search}%");
			});
		}

		$books = $query->latest('deleted_at')->paginate(15);
		$trashedCount = Book::onlyTrashed()->count();

		return view('admin.library.books.trash', compact('books', 'trashedCount'));
	}

	public function restore($id)
	{
		try {
			$book = Book::onlyTrashed()->findOrFail($id);
			$book->restore();

			return redirect()->route('admin.library.books.trash')
				->with('success', "Book '{$book->title}' restored successfully.");
		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function forceDelete($id)
	{
		try {
			$book = Book::onlyTrashed()->findOrFail($id);

			// Delete cover image if exists
			if ($book->cover_image) {
				Storage::disk('public')->delete($book->cover_image);
			}

			$title = $book->title;
			$book->forceDelete();

			return redirect()->route('admin.library.books.trash')
				->with('success', "Book '{$title}' permanently deleted.");
		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function bulkRestore(Request $request)
	{
		$request->validate([
			'book_ids' => ['required', 'array', 'min:1'],
		]);

		try {
			$count = Book::onlyTrashed()->whereIn('id', $request->book_ids)->count();
			Book::onlyTrashed()->whereIn('id', $request->book_ids)->restore();

			return response()->json([
				'success' => true,
				'message' => "{$count} book(s) restored successfully.",
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
			'book_ids' => ['required', 'array', 'min:1'],
		]);

		try {
			DB::beginTransaction();

			$books = Book::onlyTrashed()->whereIn('id', $request->book_ids)->get();
			$count = $books->count();

			foreach ($books as $book) {
				// Delete cover image if exists
				if ($book->cover_image) {
					Storage::disk('public')->delete($book->cover_image);
				}
				$book->forceDelete();
			}

			DB::commit();

			return response()->json([
				'success' => true,
				'message' => "{$count} book(s) permanently deleted.",
			]);
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json([
				'success' => false,
				'message' => 'An error occurred: ' . $e->getMessage(),
			], 500);
		}
	}

	public function emptyTrash()
	{
		try {
			DB::beginTransaction();

			$books = Book::onlyTrashed()->get();
			$count = $books->count();

			foreach ($books as $book) {
				if ($book->cover_image) {
					Storage::disk('public')->delete($book->cover_image);
				}
				$book->forceDelete();
			}

			DB::commit();

			return redirect()->route('admin.library.books.trash')
				->with('success', "{$count} book(s) permanently deleted from trash.");
		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}
}

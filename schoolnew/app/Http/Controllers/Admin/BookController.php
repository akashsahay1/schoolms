<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Http\Request;
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

		return view('admin.library.books.index', compact('books', 'categories'));
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

			return redirect()->route('admin.library.books')->with('success', 'Book added successfully.');
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

			return redirect()->route('admin.library.books')->with('success', 'Book updated successfully.');
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

			if ($book->cover_image) {
				Storage::disk('public')->delete($book->cover_image);
			}

			$book->delete();
			return redirect()->route('admin.library.books')->with('success', 'Book deleted successfully.');
		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}
}

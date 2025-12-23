<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\BookCategory;

class SampleBooksSeeder extends Seeder
{
	public function run(): void
	{
		// Get categories
		$science = BookCategory::where('name', 'Science')->first();
		$math = BookCategory::where('name', 'Mathematics')->first();
		$literature = BookCategory::where('name', 'Literature')->first();
		$fiction = BookCategory::where('name', 'Fiction')->first();

		$books = [
			[
				'book_category_id' => $science?->id ?? 1,
				'title' => 'Physics for Class XII',
				'author' => 'H.C. Verma',
				'isbn' => '978-81-77092-00-1',
				'publisher' => 'Bharati Bhawan',
				'edition' => '2024 Edition',
				'published_year' => 2024,
				'total_copies' => 50,
				'available_copies' => 50,
				'price' => 695.00,
				'rack_no' => 'PHY-12-A',
				'description' => 'Comprehensive physics textbook for Class XII',
				'is_active' => true,
			],
			[
				'book_category_id' => $science?->id ?? 1,
				'title' => 'Chemistry for Class XII',
				'author' => 'N.C.E.R.T.',
				'isbn' => '978-81-74506-89-0',
				'publisher' => 'NCERT',
				'edition' => '2024 Edition',
				'published_year' => 2024,
				'total_copies' => 50,
				'available_copies' => 50,
				'price' => 150.00,
				'rack_no' => 'CHEM-12-A',
				'description' => 'NCERT Chemistry textbook for Class XII',
				'is_active' => true,
			],
			[
				'book_category_id' => $math?->id ?? 1,
				'title' => 'Mathematics for Class XII',
				'author' => 'R.D. Sharma',
				'isbn' => '978-93-23214-56-7',
				'publisher' => 'Dhanpat Rai Publications',
				'edition' => '2024 Edition',
				'published_year' => 2024,
				'total_copies' => 45,
				'available_copies' => 45,
				'price' => 850.00,
				'rack_no' => 'MATH-12-A',
				'description' => 'Complete mathematics reference for Class XII',
				'is_active' => true,
			],
			[
				'book_category_id' => $literature?->id ?? 1,
				'title' => 'English Core for Class XII',
				'author' => 'N.C.E.R.T.',
				'isbn' => '978-81-74507-12-5',
				'publisher' => 'NCERT',
				'edition' => '2024 Edition',
				'published_year' => 2024,
				'total_copies' => 40,
				'available_copies' => 40,
				'price' => 120.00,
				'rack_no' => 'ENG-12-A',
				'description' => 'NCERT English Core textbook',
				'is_active' => true,
			],
			[
				'book_category_id' => $fiction?->id ?? 1,
				'title' => 'To Kill a Mockingbird',
				'author' => 'Harper Lee',
				'isbn' => '978-00-61120-08-4',
				'publisher' => 'HarperCollins',
				'edition' => 'Reprint',
				'published_year' => 2015,
				'total_copies' => 25,
				'available_copies' => 25,
				'price' => 350.00,
				'rack_no' => 'FIC-CLASSIC-A',
				'description' => 'Classic American literature',
				'is_active' => true,
			],
		];

		foreach ($books as $bookData) {
			Book::create($bookData);
		}

		echo "Created " . count($books) . " sample books.\n";
	}
}

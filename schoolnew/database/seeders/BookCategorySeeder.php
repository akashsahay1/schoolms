<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookCategory;

class BookCategorySeeder extends Seeder
{
	public function run(): void
	{
		$categories = [
			['name' => 'Fiction', 'description' => 'Fictional books and novels', 'is_active' => true],
			['name' => 'Non-Fiction', 'description' => 'Non-fictional books', 'is_active' => true],
			['name' => 'Science', 'description' => 'Science and technology books', 'is_active' => true],
			['name' => 'Mathematics', 'description' => 'Mathematics textbooks and references', 'is_active' => true],
			['name' => 'History', 'description' => 'History and social studies books', 'is_active' => true],
			['name' => 'Literature', 'description' => 'Literature and language books', 'is_active' => true],
			['name' => 'Reference', 'description' => 'Reference books and encyclopedias', 'is_active' => true],
			['name' => 'Comics', 'description' => 'Comics and graphic novels', 'is_active' => true],
			['name' => 'Biography', 'description' => 'Biographies and autobiographies', 'is_active' => true],
			['name' => 'Children', 'description' => 'Children\'s books and stories', 'is_active' => true],
		];

		foreach ($categories as $category) {
			BookCategory::create($category);
		}
	}
}

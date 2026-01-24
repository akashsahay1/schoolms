<?php

namespace Database\Seeders;

use App\Models\WebsiteFacility;
use App\Models\WebsitePage;
use App\Models\WebsiteSlider;
use Illuminate\Database\Seeder;

class WebsitePagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default pages
        $pages = [
            [
                'slug' => 'home',
                'title' => 'Welcome to Our School',
                'meta_description' => 'Quality education for a brighter future. Join our community of learners.',
                'meta_keywords' => 'school, education, learning, students',
                'is_active' => true,
            ],
            [
                'slug' => 'about',
                'title' => 'About Our School',
                'meta_description' => 'Learn about our school\'s history, mission, vision and values.',
                'meta_keywords' => 'about, history, mission, vision, values',
                'is_active' => true,
            ],
            [
                'slug' => 'academics',
                'title' => 'Academic Programs',
                'meta_description' => 'Explore our comprehensive academic programs and curriculum.',
                'meta_keywords' => 'academics, curriculum, programs, education',
                'is_active' => true,
            ],
            [
                'slug' => 'facilities',
                'title' => 'Our Facilities',
                'meta_description' => 'World-class facilities designed to provide the best learning environment.',
                'meta_keywords' => 'facilities, infrastructure, labs, library',
                'is_active' => true,
            ],
            [
                'slug' => 'gallery',
                'title' => 'Photo Gallery',
                'meta_description' => 'Browse our photo gallery to see glimpses of school life.',
                'meta_keywords' => 'gallery, photos, images, events',
                'is_active' => true,
            ],
            [
                'slug' => 'news',
                'title' => 'News & Announcements',
                'meta_description' => 'Stay updated with the latest news and announcements.',
                'meta_keywords' => 'news, announcements, updates, notices',
                'is_active' => true,
            ],
            [
                'slug' => 'events',
                'title' => 'School Events',
                'meta_description' => 'Explore our upcoming and past school events.',
                'meta_keywords' => 'events, activities, programs, celebrations',
                'is_active' => true,
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'meta_description' => 'Get in touch with us. We\'d love to hear from you.',
                'meta_keywords' => 'contact, address, phone, email',
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            WebsitePage::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }

        // Create sample facilities
        $facilities = [
            ['title' => 'Smart Classrooms', 'description' => 'Modern classrooms equipped with interactive whiteboards and audio-visual aids.', 'icon' => 'monitor', 'sort_order' => 1],
            ['title' => 'Library', 'description' => 'Well-stocked library with thousands of books and digital resources.', 'icon' => 'book', 'sort_order' => 2],
            ['title' => 'Computer Lab', 'description' => 'Modern computer labs with latest hardware and software.', 'icon' => 'cpu', 'sort_order' => 3],
            ['title' => 'Science Labs', 'description' => 'Well-equipped physics, chemistry, and biology labs.', 'icon' => 'activity', 'sort_order' => 4],
            ['title' => 'Sports Facilities', 'description' => 'Extensive sports grounds and indoor games area.', 'icon' => 'dribbble', 'sort_order' => 5],
            ['title' => 'Transport', 'description' => 'Safe and comfortable school buses with GPS tracking.', 'icon' => 'truck', 'sort_order' => 6],
        ];

        foreach ($facilities as $facility) {
            WebsiteFacility::updateOrCreate(
                ['title' => $facility['title']],
                array_merge($facility, ['is_active' => true])
            );
        }

        $this->command->info('Website pages and facilities seeded successfully.');
    }
}

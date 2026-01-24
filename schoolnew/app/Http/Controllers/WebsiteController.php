<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Notice;
use App\Models\Setting;
use App\Models\WebsiteContact;
use App\Models\WebsiteFacility;
use App\Models\WebsiteGallery;
use App\Models\WebsitePage;
use App\Models\WebsiteSlider;
use App\Models\WebsiteTestimonial;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    /**
     * Display the homepage
     */
    public function index()
    {
        $sliders = WebsiteSlider::active()->ordered()->get();
        $facilities = WebsiteFacility::active()->ordered()->limit(6)->get();
        $testimonials = WebsiteTestimonial::active()->ordered()->limit(6)->get();
        $events = Event::where('is_public', true)
            ->whereDate('start_date', '>=', now())
            ->orderBy('start_date')
            ->limit(3)
            ->get();
        $notices = Notice::where('is_published', true)
            ->whereDate('publish_date', '<=', now())
            ->orderBy('publish_date', 'desc')
            ->limit(5)
            ->get();
        $gallery = WebsiteGallery::active()->ordered()->limit(8)->get();

        $page = WebsitePage::findBySlug('home');

        return view('website.index', compact(
            'sliders',
            'facilities',
            'testimonials',
            'events',
            'notices',
            'gallery',
            'page'
        ));
    }

    /**
     * Display the about page
     */
    public function about()
    {
        $page = WebsitePage::findBySlug('about');
        $testimonials = WebsiteTestimonial::active()->ordered()->get();

        return view('website.about', compact('page', 'testimonials'));
    }

    /**
     * Display the academics page
     */
    public function academics()
    {
        $page = WebsitePage::findBySlug('academics');

        return view('website.academics', compact('page'));
    }

    /**
     * Display the facilities page
     */
    public function facilities()
    {
        $page = WebsitePage::findBySlug('facilities');
        $facilities = WebsiteFacility::active()->ordered()->get();

        return view('website.facilities', compact('page', 'facilities'));
    }

    /**
     * Display the gallery page
     */
    public function gallery(Request $request)
    {
        $page = WebsitePage::findBySlug('gallery');
        $categories = WebsiteGallery::getCategories();
        $category = $request->get('category');

        $gallery = WebsiteGallery::active()
            ->ordered()
            ->category($category)
            ->paginate(12);

        return view('website.gallery', compact('page', 'categories', 'category', 'gallery'));
    }

    /**
     * Display the news/announcements page
     */
    public function news(Request $request)
    {
        $page = WebsitePage::findBySlug('news');

        $notices = Notice::where('is_published', true)
            ->whereDate('publish_date', '<=', now())
            ->orderBy('publish_date', 'desc')
            ->paginate(10);

        return view('website.news', compact('page', 'notices'));
    }

    /**
     * Display a single news/notice
     */
    public function newsShow(Notice $notice)
    {
        if (!$notice->is_published || $notice->publish_date > now()) {
            abort(404);
        }

        return view('website.news-show', compact('notice'));
    }

    /**
     * Display the events page
     */
    public function events(Request $request)
    {
        $page = WebsitePage::findBySlug('events');

        $events = Event::where('is_public', true)
            ->orderBy('start_date', 'desc')
            ->paginate(9);

        return view('website.events', compact('page', 'events'));
    }

    /**
     * Display a single event
     */
    public function eventShow(Event $event)
    {
        if (!$event->is_public) {
            abort(404);
        }

        return view('website.event-show', compact('event'));
    }

    /**
     * Display the contact page
     */
    public function contact()
    {
        $page = WebsitePage::findBySlug('contact');

        return view('website.contact', compact('page'));
    }

    /**
     * Handle contact form submission
     */
    public function contactStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        WebsiteContact::create($validated);

        return back()->with('success', 'Thank you for contacting us. We will get back to you soon.');
    }
}

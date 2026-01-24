<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteContact;
use App\Models\WebsiteFacility;
use App\Models\WebsiteGallery;
use App\Models\WebsitePage;
use App\Models\WebsiteSection;
use App\Models\WebsiteSlider;
use App\Models\WebsiteTestimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebsiteSettingController extends Controller
{
    /**
     * Display website settings dashboard
     */
    public function index()
    {
        $stats = [
            'sliders' => WebsiteSlider::count(),
            'facilities' => WebsiteFacility::count(),
            'testimonials' => WebsiteTestimonial::count(),
            'gallery' => WebsiteGallery::count(),
            'pages' => WebsitePage::count(),
            'contacts' => WebsiteContact::where('status', 'new')->count(),
        ];

        return view('admin.website.index', compact('stats'));
    }

    // ==================== SLIDERS ====================

    public function sliders()
    {
        $sliders = WebsiteSlider::ordered()->get();
        return view('admin.website.sliders.index', compact('sliders'));
    }

    public function createSlider()
    {
        return view('admin.website.sliders.create');
    }

    public function storeSlider(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('website/sliders', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        WebsiteSlider::create($validated);

        return redirect()->route('admin.website.sliders')->with('success', 'Slider created successfully.');
    }

    public function editSlider(WebsiteSlider $slider)
    {
        return view('admin.website.sliders.edit', compact('slider'));
    }

    public function updateSlider(Request $request, WebsiteSlider $slider)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($slider->image) {
                Storage::disk('public')->delete($slider->image);
            }
            $validated['image'] = $request->file('image')->store('website/sliders', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $slider->update($validated);

        return redirect()->route('admin.website.sliders')->with('success', 'Slider updated successfully.');
    }

    public function destroySlider(WebsiteSlider $slider)
    {
        if ($slider->image) {
            Storage::disk('public')->delete($slider->image);
        }
        $slider->delete();

        return redirect()->route('admin.website.sliders')->with('success', 'Slider deleted successfully.');
    }

    // ==================== FACILITIES ====================

    public function facilities()
    {
        $facilities = WebsiteFacility::ordered()->get();
        return view('admin.website.facilities.index', compact('facilities'));
    }

    public function createFacility()
    {
        return view('admin.website.facilities.create');
    }

    public function storeFacility(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('website/facilities', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        WebsiteFacility::create($validated);

        return redirect()->route('admin.website.facilities')->with('success', 'Facility created successfully.');
    }

    public function editFacility(WebsiteFacility $facility)
    {
        return view('admin.website.facilities.edit', compact('facility'));
    }

    public function updateFacility(Request $request, WebsiteFacility $facility)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($facility->image) {
                Storage::disk('public')->delete($facility->image);
            }
            $validated['image'] = $request->file('image')->store('website/facilities', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $facility->update($validated);

        return redirect()->route('admin.website.facilities')->with('success', 'Facility updated successfully.');
    }

    public function destroyFacility(WebsiteFacility $facility)
    {
        if ($facility->image) {
            Storage::disk('public')->delete($facility->image);
        }
        $facility->delete();

        return redirect()->route('admin.website.facilities')->with('success', 'Facility deleted successfully.');
    }

    // ==================== TESTIMONIALS ====================

    public function testimonials()
    {
        $testimonials = WebsiteTestimonial::ordered()->get();
        return view('admin.website.testimonials.index', compact('testimonials'));
    }

    public function createTestimonial()
    {
        return view('admin.website.testimonials.create');
    }

    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'content' => 'required|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'rating' => 'nullable|integer|min:1|max:5',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('website/testimonials', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['rating'] = $validated['rating'] ?? 5;

        WebsiteTestimonial::create($validated);

        return redirect()->route('admin.website.testimonials')->with('success', 'Testimonial created successfully.');
    }

    public function editTestimonial(WebsiteTestimonial $testimonial)
    {
        return view('admin.website.testimonials.edit', compact('testimonial'));
    }

    public function updateTestimonial(Request $request, WebsiteTestimonial $testimonial)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'content' => 'required|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'rating' => 'nullable|integer|min:1|max:5',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            if ($testimonial->photo) {
                Storage::disk('public')->delete($testimonial->photo);
            }
            $validated['photo'] = $request->file('photo')->store('website/testimonials', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $testimonial->update($validated);

        return redirect()->route('admin.website.testimonials')->with('success', 'Testimonial updated successfully.');
    }

    public function destroyTestimonial(WebsiteTestimonial $testimonial)
    {
        if ($testimonial->photo) {
            Storage::disk('public')->delete($testimonial->photo);
        }
        $testimonial->delete();

        return redirect()->route('admin.website.testimonials')->with('success', 'Testimonial deleted successfully.');
    }

    // ==================== GALLERY ====================

    public function gallery()
    {
        $gallery = WebsiteGallery::ordered()->get();
        $categories = WebsiteGallery::getCategories();
        return view('admin.website.gallery.index', compact('gallery', 'categories'));
    }

    public function createGallery()
    {
        $categories = WebsiteGallery::getCategories();
        return view('admin.website.gallery.create', compact('categories'));
    }

    public function storeGallery(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('website/gallery', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        WebsiteGallery::create($validated);

        return redirect()->route('admin.website.gallery')->with('success', 'Gallery image added successfully.');
    }

    public function editGallery(WebsiteGallery $gallery)
    {
        $categories = WebsiteGallery::getCategories();
        return view('admin.website.gallery.edit', compact('gallery', 'categories'));
    }

    public function updateGallery(Request $request, WebsiteGallery $gallery)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($gallery->image) {
                Storage::disk('public')->delete($gallery->image);
            }
            $validated['image'] = $request->file('image')->store('website/gallery', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $gallery->update($validated);

        return redirect()->route('admin.website.gallery')->with('success', 'Gallery image updated successfully.');
    }

    public function destroyGallery(WebsiteGallery $gallery)
    {
        if ($gallery->image) {
            Storage::disk('public')->delete($gallery->image);
        }
        $gallery->delete();

        return redirect()->route('admin.website.gallery')->with('success', 'Gallery image deleted successfully.');
    }

    // ==================== PAGES ====================

    public function pages()
    {
        $pages = WebsitePage::all();
        return view('admin.website.pages.index', compact('pages'));
    }

    public function editPage(WebsitePage $page)
    {
        return view('admin.website.pages.edit', compact('page'));
    }

    public function updatePage(Request $request, WebsitePage $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('banner_image')) {
            if ($page->banner_image) {
                Storage::disk('public')->delete($page->banner_image);
            }
            $validated['banner_image'] = $request->file('banner_image')->store('website/pages', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $page->update($validated);

        return redirect()->route('admin.website.pages')->with('success', 'Page updated successfully.');
    }

    // ==================== CONTACT MESSAGES ====================

    public function contacts(Request $request)
    {
        $query = WebsiteContact::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $contacts = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.website.contacts.index', compact('contacts'));
    }

    public function showContact(WebsiteContact $contact)
    {
        $contact->markAsRead();
        return view('admin.website.contacts.show', compact('contact'));
    }

    public function replyContact(Request $request, WebsiteContact $contact)
    {
        $validated = $request->validate([
            'reply' => 'required|string|max:2000',
        ]);

        $contact->sendReply($validated['reply']);

        // TODO: Send email to the contact

        return redirect()->route('admin.website.contacts.show', $contact)->with('success', 'Reply sent successfully.');
    }

    public function destroyContact(WebsiteContact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.website.contacts')->with('success', 'Message deleted successfully.');
    }
}

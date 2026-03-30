<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\WebsiteContact;
use App\Models\WebsiteFacility;
use App\Models\WebsiteGallery;
use App\Models\WebsitePage;
use App\Models\WebsiteSection;
use App\Models\WebsiteSlider;
use App\Models\WebsiteTestimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
        $sections = WebsiteSection::where('page_id', $page->id)->ordered()->get();
        $layouts = WebsiteSection::LAYOUTS;
        return view('admin.website.pages.edit', compact('page', 'sections', 'layouts'));
    }

    public function updatePage(Request $request, WebsitePage $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner_color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('banner_image')) {
            if ($page->banner_image) {
                Storage::disk('public')->delete($page->banner_image);
            }
            $validated['banner_image'] = $request->file('banner_image')->store('website/pages', 'public');
        }

        // Handle banner image removal
        if ($request->has('remove_banner_image') && $request->remove_banner_image == '1') {
            if ($page->banner_image) {
                Storage::disk('public')->delete($page->banner_image);
            }
            $validated['banner_image'] = null;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['banner_color'] = $request->input('banner_color') ?: null;

        $page->update($validated);

        return redirect()->route('admin.website.pages.edit', $page)->with('success', 'Page updated successfully.');
    }

    // ==================== PAGE SECTIONS ====================

    public function storeSection(Request $request, WebsitePage $page)
    {
        $validated = $request->validate([
            'layout' => 'required|in:' . implode(',', array_keys(WebsiteSection::LAYOUTS)),
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'link' => 'nullable|string|max:255',
            'link_text' => 'nullable|string|max:100',
            'bg_color' => 'nullable|string|max:7',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('website/sections', 'public');
        }

        $maxOrder = WebsiteSection::where('page_id', $page->id)->max('sort_order') ?? 0;

        WebsiteSection::create([
            'page_id' => $page->id,
            'section_key' => 'section_' . time(),
            'layout' => $validated['layout'],
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'content' => $validated['content'],
            'image' => $imagePath,
            'link' => $validated['link'],
            'link_text' => $validated['link_text'],
            'bg_color' => $validated['bg_color'],
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        return redirect()->route('admin.website.pages.edit', $page)->with('success', 'Section added successfully.');
    }

    public function updateSection(Request $request, WebsiteSection $section)
    {
        $validated = $request->validate([
            'layout' => 'required|in:' . implode(',', array_keys(WebsiteSection::LAYOUTS)),
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'link' => 'nullable|string|max:255',
            'link_text' => 'nullable|string|max:100',
            'bg_color' => 'nullable|string|max:7',
        ]);

        if ($request->hasFile('image')) {
            if ($section->image) {
                Storage::disk('public')->delete($section->image);
            }
            $validated['image'] = $request->file('image')->store('website/sections', 'public');
        }

        if ($request->has('remove_image') && $request->remove_image == '1') {
            if ($section->image) {
                Storage::disk('public')->delete($section->image);
            }
            $validated['image'] = null;
        }

        $section->update($validated);

        return redirect()->route('admin.website.pages.edit', $section->page_id)->with('success', 'Section updated successfully.');
    }

    public function destroySection(WebsiteSection $section)
    {
        $pageId = $section->page_id;

        if ($section->image) {
            Storage::disk('public')->delete($section->image);
        }

        $section->delete();

        return redirect()->route('admin.website.pages.edit', $pageId)->with('success', 'Section deleted.');
    }

    public function reorderSections(Request $request)
    {
        $order = $request->input('order', []);
        foreach ($order as $index => $id) {
            WebsiteSection::where('id', $id)->update(['sort_order' => $index]);
        }
        return response()->json(['success' => true]);
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

        // Send reply email to the contact
        if ($contact->email && config('mail.default') !== 'log') {
            try {
                Mail::send('emails.contact-reply', [
                    'contactName' => $contact->name,
                    'contactSubject' => $contact->subject,
                    'contactMessage' => $contact->message,
                    'replyMessage' => $validated['reply'],
                    'schoolName' => config('app.name'),
                ], function ($mail) use ($contact) {
                    $mail->to($contact->email)
                         ->subject('Re: ' . $contact->subject);
                });
            } catch (\Exception $e) {
                \Log::warning("Contact reply email failed: " . $e->getMessage());
                return redirect()->route('admin.website.contacts.show', $contact)
                    ->with('success', 'Reply saved but email delivery failed.');
            }
        }

        return redirect()->route('admin.website.contacts.show', $contact)->with('success', 'Reply sent successfully.');
    }

    public function destroyContact(WebsiteContact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.website.contacts')->with('success', 'Message deleted successfully.');
    }

    // ==================== WEBSITE IMAGES ====================

    public function images()
    {
        $pages = WebsitePage::all();
        return view('admin.website.images', compact('pages'));
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
            'type' => 'required|string',
            'id' => 'nullable|integer',
        ]);

        $path = $request->file('image')->store('website/images', 'public');

        if ($request->type === 'page_banner') {
            $page = WebsitePage::findOrFail($request->id);
            if ($page->banner_image) {
                Storage::disk('public')->delete($page->banner_image);
            }
            $page->update(['banner_image' => $path]);
        } elseif ($request->type === 'setting') {
            $key = $request->input('key');
            $old = Setting::get($key);
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            Setting::set($key, $path);
        }

        return response()->json(['success' => true, 'path' => asset('storage/' . $path)]);
    }

    public function deleteImage(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'id' => 'nullable|integer',
        ]);

        if ($request->type === 'page_banner') {
            $page = WebsitePage::findOrFail($request->id);
            if ($page->banner_image) {
                Storage::disk('public')->delete($page->banner_image);
            }
            $page->update(['banner_image' => null]);
        } elseif ($request->type === 'setting') {
            $key = $request->input('key');
            $old = Setting::get($key);
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            Setting::set($key, null);
        }

        return response()->json(['success' => true]);
    }

    // ==================== HOMEPAGE SECTIONS ====================

    public function homepageSections()
    {
        return view('admin.website.homepage-sections');
    }

    public function updateHomepageSections(Request $request)
    {
        $fields = [
            // Why Choose Us section
            'homepage_why_title', 'homepage_why_subtitle',
            'homepage_why_1_icon', 'homepage_why_1_title', 'homepage_why_1_desc',
            'homepage_why_2_icon', 'homepage_why_2_title', 'homepage_why_2_desc',
            'homepage_why_3_icon', 'homepage_why_3_title', 'homepage_why_3_desc',
            'homepage_why_4_icon', 'homepage_why_4_title', 'homepage_why_4_desc',
            // About section
            'homepage_about_subtitle', 'homepage_about_title', 'homepage_about_description',
            'homepage_about_check_1', 'homepage_about_check_2', 'homepage_about_check_3',
            'homepage_about_check_4', 'homepage_about_check_5', 'homepage_about_check_6',
            // Stats section
            'total_students', 'total_teachers', 'school_years', 'awards_count',
            'stat_1_label', 'stat_2_label', 'stat_3_label', 'stat_4_label',
            // CTA section
            'cta_heading', 'cta_subtitle', 'cta_button_text', 'cta_button_link',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->input($field));
            }
        }

        // Handle about image upload
        if ($request->hasFile('homepage_about_image')) {
            $old = Setting::get('homepage_about_image');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            Setting::set('homepage_about_image', $request->file('homepage_about_image')->store('website', 'public'));
        }

        // Handle CTA background image upload
        if ($request->hasFile('cta_bg_image')) {
            $old = Setting::get('cta_bg_image');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            Setting::set('cta_bg_image', $request->file('cta_bg_image')->store('website', 'public'));
        }

        return redirect()->route('admin.website.homepage-sections')
            ->with('success', 'Homepage sections updated successfully.');
    }
}

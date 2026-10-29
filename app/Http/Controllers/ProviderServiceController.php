<?php

namespace App\Http\Controllers;

use App\Enums\ServiceStatus;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Category;
use App\Models\Service;
use App\Services\AuditService;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProviderServiceController extends Controller
{
    public function __construct(
        protected AuditService $audit
    ) {}

    public function index(Request $request): View
    {
        $profile = $request->user()->providerProfile;
        if (!$profile) {
            abort(403);
        }

        $status = $request->query('status');
        $query = Service::where('provider_profile_id', $profile->id)->with(['category', 'county']);

        if ($status && in_array($status, ['draft', 'pending_review', 'active', 'rejected', 'archived'])) {
            $query->where('status', $status);
        }

        $services = $query->latest('updated_at')->paginate(10)->withQueryString();

        return view('provider.services.index', compact('services', 'status', 'profile'));
    }

    public function create(): View
    {
        $profile = auth()->user()->providerProfile;
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('provider.services.create', compact('profile', 'categories'));
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->providerProfile;

        $action = $request->input('action', 'draft');
        $status = ServiceStatus::Draft;

        if ($action === 'submit') {
            if (!$profile->isApproved()) {
                return back()->withInput()->with('error', 'You cannot submit services for review while your provider account is pending approval.');
            }
            $status = ServiceStatus::PendingReview;
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store("services/{$profile->id}", 'public');
        }

        $priceCents = Money::parse($request->input('price'));
        $slug = $this->generateUniqueSlug($request->input('title'));

        $service = Service::create([
            'provider_profile_id' => $profile->id,
            'category_id' => $request->input('category_id'),
            'title' => $request->input('title'),
            'slug' => $slug,
            'description' => $request->input('description'),
            'inclusions' => $request->input('inclusions'),
            'exclusions' => $request->input('exclusions'),
            'price_cents' => $priceCents,
            'currency' => 'EUR',
            'duration_minutes' => $request->input('duration_minutes'),
            'county_id' => $profile->county_id,
            'image_path' => $imagePath,
            'status' => $status,
        ]);

        $this->audit->log('service.created', $service, [
            'title' => $service->title,
            'status' => $service->status->value,
        ], $user);

        $msg = $status === ServiceStatus::PendingReview
            ? 'Service created and submitted for administrator review.'
            : 'Service saved as draft.';

        return redirect()->route('provider.services.index')->with('success', $msg);
    }

    public function edit(Service $service): View
    {
        $this->authorize('update', $service);

        $profile = auth()->user()->providerProfile;
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('provider.services.edit', compact('service', 'profile', 'categories'));
    }

    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $this->authorize('update', $service);

        $user = $request->user();
        $profile = $user->providerProfile;
        $action = $request->input('action', 'save');

        $status = $service->status;

        // Editing active service sets status back to pending_review
        if ($service->status === ServiceStatus::Active) {
            $status = ServiceStatus::PendingReview;
        } elseif ($action === 'submit') {
            if (!$profile->isApproved()) {
                return back()->withInput()->with('error', 'You cannot submit services for review while your provider account is pending approval.');
            }
            $status = ServiceStatus::PendingReview;
        }

        $imagePath = $service->image_path;
        if ($request->boolean('remove_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
                $imagePath = null;
            }
        } elseif ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store("services/{$profile->id}", 'public');
        }

        $priceCents = Money::parse($request->input('price'));

        $service->update([
            'category_id' => $request->input('category_id'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'inclusions' => $request->input('inclusions'),
            'exclusions' => $request->input('exclusions'),
            'price_cents' => $priceCents,
            'duration_minutes' => $request->input('duration_minutes'),
            'image_path' => $imagePath,
            'status' => $status,
        ]);

        $this->audit->log('service.updated', $service, [
            'title' => $service->title,
            'status' => $service->status->value,
        ], $user);

        $msg = $status === ServiceStatus::PendingReview
            ? 'Service updated and submitted for review.'
            : 'Service updated successfully.';

        return redirect()->route('provider.services.index')->with('success', $msg);
    }

    public function submit(Service $service): RedirectResponse
    {
        $this->authorize('submit', $service);

        $service->update([
            'status' => ServiceStatus::PendingReview,
        ]);

        $this->audit->log('service.submitted', $service, [], auth()->user());

        return redirect()->route('provider.services.index')
            ->with('success', 'Service has been submitted for administrator review.');
    }

    public function archive(Service $service): RedirectResponse
    {
        $this->authorize('archive', $service);

        $service->update([
            'status' => ServiceStatus::Archived,
        ]);

        $this->audit->log('service.archived', $service, [], auth()->user());

        return redirect()->route('provider.services.index')
            ->with('success', 'Service has been archived.');
    }

    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 2;

        while (Service::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}

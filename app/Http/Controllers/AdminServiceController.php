<?php

namespace App\Http\Controllers;

use App\Enums\ServiceStatus;
use App\Http\Requests\RejectServiceRequest;
use App\Models\Category;
use App\Models\County;
use App\Models\Service;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminServiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Service::with(['providerProfile.user', 'category', 'county'])
            ->withCount('bookings');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        if ($request->filled('county_id')) {
            $query->where('county_id', $request->query('county_id'));
        }

        if ($request->filled('q')) {
            $term = '%' . $request->query('q') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('description', 'like', $term)
                  ->orWhereHas('providerProfile', function ($pq) use ($term) {
                      $pq->where('business_name', 'like', $term);
                  });
            });
        }

        $services = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $counties = County::orderBy('name')->get();

        return view('admin.services.index', [
            'services' => $services,
            'categories' => $categories,
            'counties' => $counties,
            'filters' => $request->only(['status', 'category_id', 'county_id', 'q']),
        ]);
    }

    public function approve(Service $service, AuditService $audit, NotificationService $notifications): RedirectResponse
    {
        $service->update([
            'status' => ServiceStatus::Active,
            'published_at' => $service->published_at ?? now(),
            'moderation_note' => null,
        ]);

        $audit->log('service.approved', $service, [
            'service_id' => $service->id,
            'title' => $service->title,
        ]);

        $notifications->serviceApproved($service);

        return back()->with('status', 'Service approved and published successfully.');
    }

    public function reject(RejectServiceRequest $request, Service $service, AuditService $audit, NotificationService $notifications): RedirectResponse
    {
        $note = $request->input('note');

        $service->update([
            'status' => ServiceStatus::Rejected,
            'moderation_note' => $note,
        ]);

        $audit->log('service.rejected', $service, [
            'service_id' => $service->id,
            'title' => $service->title,
            'note' => $note,
        ]);

        $notifications->serviceRejected($service, $note);

        return back()->with('status', 'Service has been rejected.');
    }

    public function archive(Service $service, AuditService $audit, NotificationService $notifications): RedirectResponse
    {
        $service->update([
            'status' => ServiceStatus::Archived,
        ]);

        $audit->log('service.archived', $service, [
            'service_id' => $service->id,
            'title' => $service->title,
        ]);

        if ($service->providerProfile && $service->providerProfile->user) {
            $notifications->notify(
                $service->providerProfile->user,
                'Service archived',
                "Your service '{$service->title}' has been archived by an administrator.",
                route('provider.services.index'),
                'service_archived'
            );
        }

        return back()->with('status', 'Service archived successfully.');
    }
}

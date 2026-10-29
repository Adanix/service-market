<?php

namespace App\Http\Controllers;

use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminContactController extends Controller
{
    public function index(Request $request): View
    {
        $query = ContactMessage::query();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('q')) {
            $term = '%' . $request->query('q') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('subject', 'like', $term)
                  ->orWhere('message', 'like', $term);
            });
        }

        $messages = $query->latest()->paginate(15)->withQueryString();

        return view('admin.contact.index', [
            'messages' => $messages,
            'filters' => $request->only(['status', 'q']),
        ]);
    }

    public function markRead(ContactMessage $message): RedirectResponse
    {
        $message->update([
            'status' => ContactMessageStatus::Read,
            'read_at' => now(),
        ]);

        return back()->with('status', 'Message marked as read.');
    }
}

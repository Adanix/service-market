<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPaymentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payment::with(['booking.customer', 'booking.providerProfile']);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('q')) {
            $term = '%' . $request->query('q') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('reference', 'like', $term)
                  ->orWhere('gateway_reference', 'like', $term)
                  ->orWhere('card_last4', 'like', $term)
                  ->orWhereHas('booking', function ($bq) use ($term) {
                      $bq->where('reference', 'like', $term)
                         ->orWhere('service_title', 'like', $term);
                  });
            });
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        return view('admin.payments.index', [
            'payments' => $payments,
            'filters' => $request->only(['status', 'q']),
        ]);
    }

    public function capture(Payment $payment, PaymentService $paymentService): RedirectResponse
    {
        try {
            $paymentService->capture($payment, request()->user());
            return back()->with('status', 'Payment captured successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['payment' => $e->getMessage()]);
        }
    }

    public function void(Request $request, Payment $payment, PaymentService $paymentService): RedirectResponse
    {
        try {
            $reason = $request->input('reason', 'Voided by administrator');
            $paymentService->void($payment, $reason, $request->user());
            return back()->with('status', 'Payment authorization voided successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['payment' => $e->getMessage()]);
        }
    }

    public function refund(Request $request, Payment $payment, PaymentService $paymentService): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:3', 'max:500'],
        ]);

        try {
            $paymentService->refund($payment, $request->input('reason'), $request->user());
            return back()->with('status', 'Payment refunded successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['payment' => $e->getMessage()]);
        }
    }
}

@extends('layouts.print')

@section('title', 'Receipt - ' . $booking->reference)

@section('content')
<div class="bg-white p-8 sm:p-10 rounded-2xl shadow-sm border border-stone-200">
    {{-- Header --}}
    <div class="flex items-start justify-between border-b border-stone-200 pb-6 mb-6">
        <div>
            <div class="text-2xl font-extrabold text-stone-900 tracking-tight">ServiceLocal</div>
            <div class="text-xs text-stone-500 mt-1">Republic of Ireland Local Services Marketplace</div>
            <div class="text-xs text-stone-400 mt-0.5">VAT: IE9823412K &bull; Support: support@servicelocal.test</div>
        </div>
        <div class="text-right">
            <span class="inline-block px-3 py-1 bg-stone-100 text-stone-800 text-xs font-bold rounded uppercase tracking-wider mb-2">
                Official Receipt
            </span>
            <div class="font-mono text-sm font-bold text-stone-900">{{ $payment->reference }}</div>
            <div class="text-xs text-stone-500 mt-0.5">Date: {{ $payment->captured_at ? $payment->captured_at->format('d/m/Y') : now()->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- Parties --}}
    <div class="grid grid-cols-2 gap-6 mb-8 text-xs text-stone-600">
        <div>
            <span class="font-bold text-stone-900 block mb-1 uppercase tracking-wider text-[11px]">Billed To</span>
            <div class="font-bold text-stone-800 text-sm">{{ $booking->customer->name }}</div>
            <div>{{ $booking->customer->email }}</div>
            <div>{{ $booking->customer->county?->name ?? $booking->county_name }}, Ireland</div>
        </div>
        <div>
            <span class="font-bold text-stone-900 block mb-1 uppercase tracking-wider text-[11px]">Service Provider</span>
            <div class="font-bold text-stone-800 text-sm">{{ $booking->provider_name }}</div>
            <div>County: {{ $booking->county_name }}</div>
            <div>Booking Ref: <span class="font-mono">{{ $booking->reference }}</span></div>
        </div>
    </div>

    {{-- Line Item Table --}}
    <div class="border border-stone-200 rounded-xl overflow-hidden mb-6">
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-stone-700 font-bold text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Description</th>
                    <th class="px-4 py-3 text-center">County</th>
                    <th class="px-4 py-3 text-right">Amount (EUR)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                <tr>
                    <td class="px-4 py-4">
                        <div class="font-bold text-stone-900">{{ $booking->service_title }}</div>
                        <div class="text-xs text-stone-500">Service completed on {{ $booking->completed_at ? $booking->completed_at->format('d/m/Y H:i') : 'Scheduled' }}</div>
                    </td>
                    <td class="px-4 py-4 text-center text-xs text-stone-600">
                        {{ $booking->county_name }}
                    </td>
                    <td class="px-4 py-4 text-right font-bold text-stone-900">
                        <x-ui.money :cents="$booking->price_cents" />
                    </td>
                </tr>
            </tbody>
            <tfoot class="bg-stone-50 text-xs font-semibold">
                <tr>
                    <td colspan="2" class="px-4 py-2.5 text-right text-stone-600">Subtotal</td>
                    <td class="px-4 py-2.5 text-right font-bold text-stone-900"><x-ui.money :cents="$booking->price_cents" /></td>
                </tr>
                <tr>
                    <td colspan="2" class="px-4 py-2.5 text-right text-stone-600">VAT (0% Intermediary)</td>
                    <td class="px-4 py-2.5 text-right font-bold text-stone-900">€0.00</td>
                </tr>
                <tr class="border-t-2 border-stone-300">
                    <td colspan="2" class="px-4 py-3 text-right text-sm font-bold text-stone-900">Total Paid</td>
                    <td class="px-4 py-3 text-right text-base font-extrabold text-stone-900"><x-ui.money :cents="$booking->price_cents" /></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Payment Details --}}
    <div class="bg-stone-50 p-4 rounded-xl border border-stone-200 text-xs text-stone-600 space-y-1 mb-8">
        <div class="font-bold text-stone-900 mb-1">Payment Information</div>
        <div class="flex justify-between">
            <span>Payment Method:</span>
            <span class="font-mono text-stone-800 font-medium">{{ strtoupper($payment->card_brand) }} ending in {{ $payment->card_last4 }}</span>
        </div>
        <div class="flex justify-between">
            <span>Payment Status:</span>
            <span class="font-semibold text-emerald-700 capitalize">{{ $payment->status->value }}</span>
        </div>
        <div class="flex justify-between">
            <span>Transaction Reference:</span>
            <span class="font-mono text-stone-700">{{ $payment->gateway_reference }}</span>
        </div>
    </div>

    {{-- Print & Return Actions --}}
    <div class="no-print flex items-center justify-between pt-4 border-t border-stone-200">
        <a href="{{ route('customer.bookings.show', $booking->reference) }}" class="text-xs font-semibold text-stone-600 hover:text-stone-900">
            &larr; Back to Booking
        </a>
        <button type="button" onclick="window.print()" class="px-4 py-2 bg-stone-900 text-white font-semibold text-xs rounded-xl hover:bg-stone-800 transition-colors">
            Print / Save PDF
        </button>
    </div>
</div>
@endsection

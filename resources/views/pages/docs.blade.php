<x-layouts.app title="Documentation - ServiceLocal Marketplace Specs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="mb-10 pb-6 border-b border-stone-200">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 mb-3">
                <x-ui.icon name="file-text" class="w-3.5 h-3.5" />
                Technical Specification & Architectural Guide
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">Marketplace Architecture & Operations Guide</h1>
            <p class="mt-2 text-base text-stone-600 max-w-3xl">
                This document details every domain rule, data entity, booking state machine, payment authorization lifecycle, and administrative operation powering the ServiceLocal platform.
            </p>
        </div>

        {{-- Mobile Table of Contents (Non-sticky, collapsible) --}}
        <div class="block lg:hidden mb-8" x-data="{ open: false }">
            <button @click="open = !open" 
                    type="button" 
                    class="w-full flex items-center justify-between px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-left text-sm font-semibold text-stone-800 focus:outline-none focus:ring-2 focus:ring-teal-600 transition">
                <span class="flex items-center gap-2">
                    <x-ui.icon name="file-text" class="w-4 h-4 text-stone-500" />
                    <span>Table of Contents (12 Sections)</span>
                </span>
                <x-ui.icon name="chevron-down" class="w-4 h-4 text-stone-500 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
            </button>
            
            <div x-show="open" 
                 x-cloak 
                 class="mt-2 bg-stone-50 border border-stone-200 rounded-xl p-4 text-sm">
                <ol class="space-y-2 text-stone-700">
                    <li><a href="#section-1" @click="open = false" class="hover:text-teal-700 block py-1">1. Executive summary</a></li>
                    <li><a href="#section-2" @click="open = false" class="hover:text-teal-700 block py-1">2. Domain model and entity relationships</a></li>
                    <li><a href="#section-3" @click="open = false" class="hover:text-teal-700 block py-1">3. Geographic scope (Republic of Ireland)</a></li>
                    <li><a href="#section-4" @click="open = false" class="hover:text-teal-700 block py-1">4. User roles and authorization</a></li>
                    <li><a href="#section-5" @click="open = false" class="hover:text-teal-700 block py-1">5. Provider lifecycle and service catalog</a></li>
                    <li><a href="#section-6" @click="open = false" class="hover:text-teal-700 block py-1">6. Booking and payment lifecycle</a></li>
                    <li><a href="#section-7" @click="open = false" class="hover:text-teal-700 block py-1">7. Money handling and payout mechanics</a></li>
                    <li><a href="#section-8" @click="open = false" class="hover:text-teal-700 block py-1">8. Audit trail and moderation</a></li>
                    <li><a href="#section-9" @click="open = false" class="hover:text-teal-700 block py-1">9. Architecture and technology stack</a></li>
                    <li><a href="#section-10" @click="open = false" class="hover:text-teal-700 block py-1">10. Seeded demo data and test personas</a></li>
                    <li><a href="#section-11" @click="open = false" class="hover:text-teal-700 block py-1">11. Demo testing guide</a></li>
                    <li><a href="#section-12" @click="open = false" class="hover:text-teal-700 block py-1">12. Extensibility and custom development roadmap</a></li>
                </ol>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            {{-- Desktop Sticky Nav --}}
            <nav class="hidden lg:block lg:col-span-3 lg:sticky lg:top-24 lg:self-start bg-stone-50 border border-stone-200 rounded-2xl p-5 text-sm max-h-[calc(100vh-7rem)] overflow-y-auto" aria-label="Table of Contents">
                <div class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-3">Sections</div>
                <ol class="space-y-2 text-stone-700">
                    <li><a href="#section-1" class="hover:text-emerald-700 block py-0.5">1. Executive summary</a></li>
                    <li><a href="#section-2" class="hover:text-emerald-700 block py-0.5">2. Domain model and entity relationships</a></li>
                    <li><a href="#section-3" class="hover:text-emerald-700 block py-0.5">3. Geographic scope (Republic of Ireland)</a></li>
                    <li><a href="#section-4" class="hover:text-emerald-700 block py-0.5">4. User roles and authorization</a></li>
                    <li><a href="#section-5" class="hover:text-emerald-700 block py-0.5">5. Provider lifecycle and service catalog</a></li>
                    <li><a href="#section-6" class="hover:text-emerald-700 block py-0.5">6. Booking and payment lifecycle</a></li>
                    <li><a href="#section-7" class="hover:text-emerald-700 block py-0.5">7. Money handling and payout mechanics</a></li>
                    <li><a href="#section-8" class="hover:text-emerald-700 block py-0.5">8. Audit trail and moderation</a></li>
                    <li><a href="#section-9" class="hover:text-emerald-700 block py-0.5">9. Architecture and technology stack</a></li>
                    <li><a href="#section-10" class="hover:text-emerald-700 block py-0.5">10. Seeded demo data and test personas</a></li>
                    <li><a href="#section-11" class="hover:text-emerald-700 block py-0.5">11. Demo testing guide</a></li>
                    <li><a href="#section-12" class="hover:text-emerald-700 block py-0.5">12. Extensibility and custom development roadmap</a></li>
                </ol>
            </nav>

            {{-- Main Content --}}
            <div class="lg:col-span-9 space-y-16 text-stone-800 leading-relaxed">

                {{-- Section 1 --}}
                <section id="section-1" class="scroll-mt-24">
                    <h2 class="text-2xl font-bold text-stone-900 border-b border-stone-200 pb-3 mb-4">1. Executive summary</h2>
                    <p class="mb-4">
                        <strong>ServiceLocal</strong> is a two-sided local services marketplace designed for the Republic of Ireland. It connects residential and commercial customers seeking trade, domestic, automotive, and educational services with local, verified independent providers.
                    </p>
                    <p class="mb-4">
                        To guarantee mutual trust without upfront financial friction, ServiceLocal implements an <strong>escrow-like two-step payment authorization model</strong>. When a customer places a booking request, their payment method is authorized (holding funds) for up to 7 days. Funds are never captured until the provider completes the work and either the customer explicitly confirms completion or an automated confirmation window elapses.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                        <div class="bg-stone-50 border border-stone-200 rounded-xl p-4">
                            <div class="text-sm font-bold text-stone-900">Zero Upfront Capture</div>
                            <div class="text-xs text-stone-600 mt-1">Payment stays in authorization hold until verified delivery of service.</div>
                        </div>
                        <div class="bg-stone-50 border border-stone-200 rounded-xl p-4">
                            <div class="text-sm font-bold text-stone-900">Strict Moderation</div>
                            <div class="text-xs text-stone-600 mt-1">Every provider and service listing undergoes mandatory admin review before going live.</div>
                        </div>
                        <div class="bg-stone-50 border border-stone-200 rounded-xl p-4">
                            <div class="text-sm font-bold text-stone-900">Democratic Dispute Prevention</div>
                            <div class="text-xs text-stone-600 mt-1">Audited lifecycle transitions, immutable timestamps, and cancellation safety buffers.</div>
                        </div>
                    </div>
                </section>

                {{-- Section 2 --}}
                <section id="section-2" class="scroll-mt-24">
                    <h2 class="text-2xl font-bold text-stone-900 border-b border-stone-200 pb-3 mb-4">2. Domain model and entity relationships</h2>
                    <p class="mb-4">
                        The data architecture consists of normalized Eloquent entities stored in SQLite, leveraging database constraints and foreign key cascades to ensure data integrity:
                    </p>
                    <ul class="list-disc pl-6 space-y-2 mb-4 text-sm text-stone-700">
                        <li><strong>County:</strong> Fixed catalog of all 26 Irish counties with code, name, and province.</li>
                        <li><strong>Category:</strong> 8 service categories with Lucide icons, slug, and active status.</li>
                        <li><strong>User:</strong> Single authentication entity with role enum (<code class="bg-stone-100 px-1 py-0.5 rounded text-xs">customer</code>, <code class="bg-stone-100 px-1 py-0.5 rounded text-xs">provider</code>, <code class="bg-stone-100 px-1 py-0.5 rounded text-xs">admin</code>) and status enum (<code class="bg-stone-100 px-1 py-0.5 rounded text-xs">active</code>, <code class="bg-stone-100 px-1 py-0.5 rounded text-xs">suspended</code>).</li>
                        <li><strong>ProviderProfile:</strong> 1-to-1 extension of User for providers. Contains business details, primary county, moderation status, and IBAN snapshot.</li>
                        <li><strong>Service:</strong> Belongs to ProviderProfile, Category, and County. Holds fixed pricing in integer cents, duration, inclusions, exclusions, and moderation state.</li>
                        <li><strong>Booking:</strong> Core transaction model. Holds historical snapshots of service title, category, provider, county, and fee breakdown, plus all lifecycle timestamps.</li>
                        <li><strong>Payment:</strong> 1-to-1 with Booking. Tracks driver, status, amount in integer cents, card brand, last 4 digits, gateway reference, and idempotency key.</li>
                        <li><strong>Payout:</strong> Aggregates completed, captured bookings for a provider across a specified date period.</li>
                        <li><strong>AuditLog:</strong> Polymorphic activity log capturing actor, action string, target entity, and sanitized metadata.</li>
                        <li><strong>ContactMessage:</strong> Support inquiries submitted through the public help centre.</li>
                    </ul>
                </section>

                {{-- Section 3 --}}
                <section id="section-3" class="scroll-mt-24">
                    <h2 class="text-2xl font-bold text-stone-900 border-b border-stone-200 pb-3 mb-4">3. Geographic scope (Republic of Ireland)</h2>
                    <p class="mb-4">
                        ServiceLocal strictly serves the 26 counties of the Republic of Ireland. Services and providers are tied to specific counties to provide localized search relevancy and realistic service availability:
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs text-stone-600 bg-stone-50 p-4 rounded-xl border border-stone-200 mb-4">
                        <div>Carlow (CW)</div><div>Cavan (CN)</div><div>Clare (CE)</div><div>Cork (CO)</div>
                        <div>Donegal (DL)</div><div>Dublin (D)</div><div>Galway (G)</div><div>Kerry (KY)</div>
                        <div>Kildare (KE)</div><div>Kilkenny (KK)</div><div>Laois (LS)</div><div>Leitrim (LM)</div>
                        <div>Limerick (LK)</div><div>Longford (LD)</div><div>Louth (LH)</div><div>Mayo (MO)</div>
                        <div>Meath (MH)</div><div>Monaghan (MN)</div><div>Offaly (OY)</div><div>Roscommon (RN)</div>
                        <div>Sligo (SO)</div><div>Tipperary (TY)</div><div>Waterford (WD)</div><div>Westmeath (WH)</div>
                        <div>Wexford (WX)</div><div>Wicklow (WW)</div>
                    </div>
                    <p class="text-sm text-stone-600">
                        When authenticated as a customer with a home county, the homepage automatically elevates popular services in their local area.
                    </p>
                </section>

                {{-- Section 4 --}}
                <section id="section-4" class="scroll-mt-24">
                    <h2 class="text-2xl font-bold text-stone-900 border-b border-stone-200 pb-3 mb-4">4. User roles and authorization</h2>
                    <p class="mb-4">
                        The platform enforces role-based access control via Laravel middleware (<code class="bg-stone-100 px-1 py-0.5 rounded text-xs">role:customer</code>, <code class="bg-stone-100 px-1 py-0.5 rounded text-xs">role:provider</code>, <code class="bg-stone-100 px-1 py-0.5 rounded text-xs">role:admin</code>) and Eloquent Policies.
                    </p>
                    <div class="overflow-x-auto border border-stone-200 rounded-xl">
                        <table class="min-w-full divide-y divide-stone-200 text-sm">
                            <thead class="bg-stone-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-stone-700">Role</th>
                                    <th class="px-4 py-3 text-left font-semibold text-stone-700">Access Scope</th>
                                    <th class="px-4 py-3 text-left font-semibold text-stone-700">Guardrails & Limitations</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200 bg-white">
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-stone-900">Customer</td>
                                    <td class="px-4 py-3 text-stone-600">Browse catalog, place booking checkout, view active/past bookings, confirm completion, cancel pending bookings, download receipts.</td>
                                    <td class="px-4 py-3 text-stone-600">Cannot book own services. Cannot access provider or administrative workspaces.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-stone-900">Provider</td>
                                    <td class="px-4 py-3 text-stone-600">Manage business profile, manage service catalog, accept/decline incoming requests, advance service progress, view net earnings and payouts.</td>
                                    <td class="px-4 py-3 text-stone-600">Cannot publish services until approved by admin. Editing published service returns it to pending moderation.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-stone-900">Administrator</td>
                                    <td class="px-4 py-3 text-stone-600">System overview dashboard, user toggle, provider approvals, service moderation, booking cancellation with optional refund, manual payment capture/void/refund, payouts, audit log, settings.</td>
                                    <td class="px-4 py-3 text-stone-600">Financial actions require explicit confirmation dialogs and generate immutable audit log records.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- Section 5 --}}
                <section id="section-5" class="scroll-mt-24">
                    <h2 class="text-2xl font-bold text-stone-900 border-b border-stone-200 pb-3 mb-4">5. Provider lifecycle and service catalog</h2>
                    <p class="mb-4">
                        To maintain high service quality, every provider must be reviewed before their catalog becomes publicly discoverable.
                    </p>
                    <div class="bg-stone-50 p-5 rounded-xl border border-stone-200 space-y-3 mb-4 text-sm">
                        <div class="flex items-start gap-3">
                            <x-ui.badge variant="warning">Pending Approval</x-ui.badge>
                            <div>Provider registers business profile. Cannot publish services yet.</div>
                        </div>
                        <div class="flex items-start gap-3">
                            <x-ui.badge variant="success">Approved</x-ui.badge>
                            <div>Admin approves provider. Provider can now submit services for review.</div>
                        </div>
                        <div class="flex items-start gap-3">
                            <x-ui.badge variant="danger">Suspended</x-ui.badge>
                            <div>Admin suspends provider with a recorded reason. All active services are removed from public search.</div>
                        </div>
                    </div>
                    <p class="text-sm text-stone-600 mb-2">
                        <strong>Service Catalog Moderation:</strong> Services exist in <code class="text-xs bg-stone-100 px-1 py-0.5 rounded">draft</code>, <code class="text-xs bg-stone-100 px-1 py-0.5 rounded">pending_approval</code>, <code class="text-xs bg-stone-100 px-1 py-0.5 rounded">active</code>, <code class="text-xs bg-stone-100 px-1 py-0.5 rounded">rejected</code>, or <code class="text-xs bg-stone-100 px-1 py-0.5 rounded">archived</code>. If an active service is updated, its status automatically returns to <code class="text-xs bg-stone-100 px-1 py-0.5 rounded">pending_approval</code> to ensure modifications remain compliant.
                    </p>
                </section>

                {{-- Section 6 --}}
                <section id="section-6" class="scroll-mt-24">
                    <h2 class="text-2xl font-bold text-stone-900 border-b border-stone-200 pb-3 mb-4">6. Booking and payment lifecycle</h2>
                    <p class="mb-4">
                        Bookings follow a strict unidirectional state machine managed by <code class="text-xs bg-stone-100 px-1 py-0.5 rounded">BookingService</code> and validated against <code class="text-xs bg-stone-100 px-1 py-0.5 rounded">BookingService::canTransition</code>:
                    </p>
                    <div class="overflow-x-auto border border-stone-200 rounded-xl mb-6">
                        <table class="min-w-full divide-y divide-stone-200 text-sm">
                            <thead class="bg-stone-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-stone-700">Booking Status</th>
                                    <th class="px-4 py-3 text-left font-semibold text-stone-700">Payment Status</th>
                                    <th class="px-4 py-3 text-left font-semibold text-stone-700">Next Action & Responsibility</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200 bg-white">
                                <tr>
                                    <td class="px-4 py-3"><x-marketplace.status-badge status="pending_provider" /></td>
                                    <td class="px-4 py-3"><x-marketplace.payment-badge status="authorized" /></td>
                                    <td class="px-4 py-3 text-stone-600">Provider must Accept or Decline within authorization window.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3"><x-marketplace.status-badge status="accepted" /></td>
                                    <td class="px-4 py-3"><x-marketplace.payment-badge status="authorized" /></td>
                                    <td class="px-4 py-3 text-stone-600">Provider marks 'Start Service' on the scheduled day.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3"><x-marketplace.status-badge status="in_progress" /></td>
                                    <td class="px-4 py-3"><x-marketplace.payment-badge status="authorized" /></td>
                                    <td class="px-4 py-3 text-stone-600">Provider finishes work and marks 'Complete Service'.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3"><x-marketplace.status-badge status="awaiting_confirmation" /></td>
                                    <td class="px-4 py-3"><x-marketplace.payment-badge status="authorized" /></td>
                                    <td class="px-4 py-3 text-stone-600">Customer confirms work (or auto-completed after {{ $settings['auto_complete_hours'] }}h). Funds captured.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3"><x-marketplace.status-badge status="completed" /></td>
                                    <td class="px-4 py-3"><x-marketplace.payment-badge status="captured" /></td>
                                    <td class="px-4 py-3 text-stone-600">Transaction complete. Funds allocated for provider payout.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3"><x-marketplace.status-badge status="declined" /></td>
                                    <td class="px-4 py-3"><x-marketplace.payment-badge status="voided" /></td>
                                    <td class="px-4 py-3 text-stone-600">Provider declined request. Authorization hold released immediately.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3"><x-marketplace.status-badge status="cancelled" /></td>
                                    <td class="px-4 py-3 text-stone-600">Voided / Refunded</td>
                                    <td class="px-4 py-3 text-stone-600">Cancelled by customer/provider/admin. Hold voided, or captured funds refunded.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3"><x-marketplace.status-badge status="expired" /></td>
                                    <td class="px-4 py-3"><x-marketplace.payment-badge status="voided" /></td>
                                    <td class="px-4 py-3 text-stone-600">Authorization window ({{ $settings['authorization_days'] }} days) elapsed without completion. Voided automatically.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- Section 7 --}}
                <section id="section-7" class="scroll-mt-24">
                    <h2 class="text-2xl font-bold text-stone-900 border-b border-stone-200 pb-3 mb-4">7. Money handling and payout mechanics</h2>
                    <p class="mb-4">
                        Financial precision is paramount in ServiceLocal. All monetary amounts are handled strictly in <strong>integer cents (EUR)</strong>. Floats are never used for currency values to prevent rounding errors.
                    </p>
                    <div class="bg-stone-50 border border-stone-200 rounded-xl p-5 mb-4 text-sm space-y-2">
                        <div><strong>Platform Commission:</strong> {{ $settings['platform_fee_bps'] / 100 }}% ({{ $settings['platform_fee_bps'] }} basis points).</div>
                        <div><strong>Fee Formula:</strong> <code class="text-xs bg-stone-200 px-1.5 py-0.5 rounded">fee_cents = intval(round(($price_cents * $bps) / 10000))</code></div>
                        <div><strong>Provider Net:</strong> <code class="text-xs bg-stone-200 px-1.5 py-0.5 rounded">net_cents = $price_cents - $fee_cents</code></div>
                    </div>
                    <p class="text-sm text-stone-600">
                        When an administrator aggregates completed bookings into a Payout, the system links the bookings to the payout entity. Marking a payout as 'Paid' transfers the records to the provider's payout history and logs an immutable audit entry.
                    </p>
                </section>

                {{-- Section 8 --}}
                <section id="section-8" class="scroll-mt-24">
                    <h2 class="text-2xl font-bold text-stone-900 border-b border-stone-200 pb-3 mb-4">8. Audit trail and moderation</h2>
                    <p class="mb-4">
                        Every state modification, payment authorization, capture, void, refund, administrative approval, and internal note is recorded in the <code class="text-xs bg-stone-100 px-1 py-0.5 rounded">audit_logs</code> table.
                    </p>
                    <p class="text-sm text-stone-600 mb-4">
                        <strong>Security and Privacy:</strong> The <code class="text-xs bg-stone-100 px-1 py-0.5 rounded">AuditService</code> strictly sanitizes metadata, stripping card numbers, CVC, expiry dates, and passwords before database persistence. Internal notes made on bookings are captured as audit action <code class="text-xs bg-stone-100 px-1 py-0.5 rounded">booking.note</code> and are strictly visible to administrators.
                    </p>
                </section>

                {{-- Section 9 --}}
                <section id="section-9" class="scroll-mt-24">
                    <h2 class="text-2xl font-bold text-stone-900 border-b border-stone-200 pb-3 mb-4">9. Architecture and technology stack</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="p-4 rounded-xl border border-stone-200 bg-white">
                            <span class="text-xs font-bold text-emerald-700 uppercase">Backend</span>
                            <div class="font-bold text-stone-900 text-base mt-1">Laravel 13 & PHP 8.5</div>
                            <p class="text-stone-600 text-xs mt-1">Robust MVC architecture, strict typed models, custom services, policy-driven authorization, and SQLite database engine.</p>
                        </div>
                        <div class="p-4 rounded-xl border border-stone-200 bg-white">
                            <span class="text-xs font-bold text-emerald-700 uppercase">Frontend</span>
                            <div class="font-bold text-stone-900 text-base mt-1">Tailwind CSS 4 & Alpine.js 3</div>
                            <p class="text-stone-600 text-xs mt-1">Semantic Blade components, Inter Variable typography, Lucide SVG iconography, and lightweight reactive UI interactions.</p>
                        </div>
                        <div class="p-4 rounded-xl border border-stone-200 bg-white">
                            <span class="text-xs font-bold text-emerald-700 uppercase">Testing & Quality</span>
                            <div class="font-bold text-stone-900 text-base mt-1">Pest v4 & PHPUnit 12</div>
                            <p class="text-stone-600 text-xs mt-1">Comprehensive feature and unit test suites covering edge cases, state transitions, hygiene, and security gates.</p>
                        </div>
                        <div class="p-4 rounded-xl border border-stone-200 bg-white">
                            <span class="text-xs font-bold text-emerald-700 uppercase">Environment</span>
                            <div class="font-bold text-stone-900 text-base mt-1">Docker & Adanix Toolchain</div>
                            <p class="text-stone-600 text-xs mt-1">Hermetic container deployment on Ubuntu 24.04 LTS with automated build pipelines and zero host contamination.</p>
                        </div>
                    </div>
                </section>

                {{-- Section 10 --}}
                <section id="section-10" class="scroll-mt-24">
                    <h2 class="text-2xl font-bold text-stone-900 border-b border-stone-200 pb-3 mb-4">10. Seeded demo data and test personas</h2>
                    <p class="mb-4 text-sm text-stone-600">
                        The demo database is pre-populated with 7 distinct user personas to facilitate instant passwordless exploration of every user journey:
                    </p>
                    <div class="overflow-x-auto border border-stone-200 rounded-xl mb-6">
                        <table class="min-w-full divide-y divide-stone-200 text-sm">
                            <thead class="bg-stone-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-stone-700">Persona</th>
                                    <th class="px-4 py-3 text-left font-semibold text-stone-700">Email</th>
                                    <th class="px-4 py-3 text-left font-semibold text-stone-700">Role</th>
                                    <th class="px-4 py-3 text-left font-semibold text-stone-700">County</th>
                                    <th class="px-4 py-3 text-left font-semibold text-stone-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200 bg-white">
                                @foreach($users as $u)
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-stone-900">{{ $u->name }}</td>
                                        <td class="px-4 py-3 text-stone-600 font-mono text-xs">{{ $u->email }}</td>
                                        <td class="px-4 py-3">
                                            <span class="capitalize text-xs font-medium px-2 py-0.5 rounded-full {{ $u->role->value === 'admin' ? 'bg-purple-100 text-purple-800' : ($u->role->value === 'provider' ? 'bg-blue-100 text-blue-800' : 'bg-stone-100 text-stone-800') }}">
                                                {{ $u->role->value }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-stone-600">{{ $u->county?->name ?? ($u->providerProfile?->county?->name ?? '—') }}</td>
                                        <td class="px-4 py-3">
                                            <form action="{{ route('demo.sign-in.as', $u->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 underline">
                                                    Sign in as
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h3 class="text-base font-bold text-stone-900 mb-3">Simulated Payment Test Cards</h3>
                    <p class="text-xs text-stone-500 mb-3">Any 3-digit CVC and future MM/YY will work with these numbers:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs font-mono">
                        @foreach($demoCards as $number => $card)
                            <div class="p-3 rounded-lg border border-stone-200 bg-stone-50 flex flex-col justify-between">
                                <div class="font-bold text-stone-900">{{ strtoupper($card['brand']) }} &bull; {{ $card['success'] ? 'Valid Card' : 'Fails: ' . ($card['failure_code'] ?? 'Declined') }}</div>
                                <div class="text-stone-600 text-sm mt-1">{{ chunk_split($number, 4, ' ') }}</div>
                                <div class="text-stone-500 font-sans text-xs mt-1">Outcome: {{ $card['success'] ? 'Authorized successfully' : ($card['failure_message'] ?? 'Declined') }}</div>
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- Section 11 --}}
                <section id="section-11" class="scroll-mt-24">
                    <h2 class="text-2xl font-bold text-stone-900 border-b border-stone-200 pb-3 mb-4">11. Demo testing guide</h2>
                    <p class="mb-4 text-sm text-stone-600">
                        Follow these curated walkthrough scenarios to test end-to-end functionality across multiple personas:
                    </p>
                    <div class="space-y-4 text-sm">
                        <div class="p-4 rounded-xl border border-stone-200 bg-white">
                            <span class="font-bold text-stone-900">Scenario A: Happy Path Booking Lifecycle</span>
                            <ol class="list-decimal pl-5 mt-2 space-y-1 text-stone-600">
                                <li>Sign in as <strong>Emma Murphy</strong> (Customer in Cork).</li>
                                <li>Browse to Lawn Mowing Service by Silva Garden Care and proceed to checkout.</li>
                                <li>Use the Successful Visa test card to authorize payment. Booking state is now <code class="text-xs bg-stone-100 px-1 py-0.5 rounded">pending_provider</code>.</li>
                                <li>Sign in as <strong>Maria Silva</strong> (Provider) and navigate to Bookings. Click <strong>Accept</strong>.</li>
                                <li>Click <strong>Start Service</strong> to mark in-progress, then <strong>Complete Service</strong>.</li>
                                <li>Switch back to Emma Murphy, review the completed booking, and click <strong>Confirm Completion</strong>.</li>
                                <li>Verify payment status changes to <code class="text-xs bg-stone-100 px-1 py-0.5 rounded">captured</code>.</li>
                            </ol>
                        </div>
                        <div class="p-4 rounded-xl border border-stone-200 bg-white">
                            <span class="font-bold text-stone-900">Scenario B: Service Moderation</span>
                            <ol class="list-decimal pl-5 mt-2 space-y-1 text-stone-600">
                                <li>Sign in as <strong>Tomas Kelly</strong> (Provider in Galway).</li>
                                <li>Create a new service listing and submit it for review.</li>
                                <li>Sign in as <strong>Aoife Brennan</strong> (Administrator). Go to <strong>Services</strong>.</li>
                                <li>Approve or reject the service with a moderation note.</li>
                            </ol>
                        </div>
                        <div class="p-4 rounded-xl border border-stone-200 bg-white">
                            <span class="font-bold text-stone-900">Scenario C: Administrative Payment Refund</span>
                            <ol class="list-decimal pl-5 mt-2 space-y-1 text-stone-600">
                                <li>Sign in as <strong>Aoife Brennan</strong> (Administrator).</li>
                                <li>Navigate to <strong>Payments</strong>, select a captured payment, and issue a refund with a mandatory reason.</li>
                                <li>Verify the booking is marked cancelled, payment marked refunded, and audit trail recorded.</li>
                            </ol>
                        </div>
                    </div>
                </section>

                {{-- Section 12 --}}
                <section id="section-12" class="scroll-mt-24">
                    <h2 class="text-2xl font-bold text-stone-900 border-b border-stone-200 pb-3 mb-4">12. Extensibility and custom development roadmap</h2>
                    <p class="mb-4 text-sm text-stone-600 leading-relaxed">
                        <strong>Yes, ServiceLocal is built with high modularity and can be fully customized or expanded according to custom business requirements.</strong> Leveraging standard Laravel 13 architecture, Eloquent ORM, Alpine.js, and Tailwind CSS v4, the system serves as a production-grade foundation that can be extended across multiple commercial dimensions:
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6 text-sm">
                        <div class="p-5 rounded-2xl bg-white border border-stone-200 shadow-xs hover:border-emerald-300 transition-colors">
                            <div class="flex items-center gap-2.5 mb-2 font-bold text-stone-900">
                                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    <x-ui.icon name="credit-card" class="w-4 h-4" />
                                </span>
                                Real Payment Gateways (Stripe Connect, Revolut, Adyen)
                            </div>
                            <p class="text-xs text-stone-600 leading-relaxed">
                                The simulated two-phase gateway implements clean separation via service contracts. It can be directly connected to Stripe Connect (Express/Custom accounts) or Adyen for automated card pre-authorizations, instant marketplace commission splits, and automated SEPA/IBAN bank transfers.
                            </p>
                        </div>

                        <div class="p-5 rounded-2xl bg-white border border-stone-200 shadow-xs hover:border-sky-300 transition-colors">
                            <div class="flex items-center gap-2.5 mb-2 font-bold text-stone-900">
                                <span class="p-2 rounded-xl bg-sky-50 text-sky-700 border border-sky-100">
                                    <x-ui.icon name="user" class="w-4 h-4" />
                                </span>
                                Full Authentication & Social Single Sign-On
                            </div>
                            <p class="text-xs text-stone-600 leading-relaxed">
                                The passwordless demo account switcher can be seamlessly replaced with standard email/password registration, password recovery, email verification (via Laravel Breeze or Fortify), and OAuth Social Login (Google, Apple, Microsoft via Laravel Socialite).
                            </p>
                        </div>

                        <div class="p-5 rounded-2xl bg-white border border-stone-200 shadow-xs hover:border-amber-300 transition-colors">
                            <div class="flex items-center gap-2.5 mb-2 font-bold text-stone-900">
                                <span class="p-2 rounded-xl bg-amber-50 text-amber-700 border border-amber-100">
                                    <x-ui.icon name="shield-check" class="w-4 h-4" />
                                </span>
                                Customer Reviews, Ratings & Reputation Badges
                            </div>
                            <p class="text-xs text-stone-600 leading-relaxed">
                                A verified review module can be added as a polymorphic relation to completed bookings, enabling customers to rate providers (1 to 5 stars), evaluate punctuality and quality, upload before/after photos, and display reputation badges on public profiles.
                            </p>
                        </div>

                        <div class="p-5 rounded-2xl bg-white border border-stone-200 shadow-xs hover:border-purple-300 transition-colors">
                            <div class="flex items-center gap-2.5 mb-2 font-bold text-stone-900">
                                <span class="p-2 rounded-xl bg-purple-50 text-purple-700 border border-purple-100">
                                    <x-ui.icon name="mail" class="w-4 h-4" />
                                </span>
                                In-App Messaging & Multi-Channel Alerts
                            </div>
                            <p class="text-xs text-stone-600 leading-relaxed">
                                Enable real-time chat between customers and providers using Laravel Reverb (WebSockets) or Pusher. Extend notifications with Twilio (SMS), WhatsApp Business Cloud API, or mobile push notifications (Firebase Cloud Messaging).
                            </p>
                        </div>

                        <div class="p-5 rounded-2xl bg-white border border-stone-200 shadow-xs hover:border-indigo-300 transition-colors">
                            <div class="flex items-center gap-2.5 mb-2 font-bold text-stone-900">
                                <span class="p-2 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    <x-ui.icon name="map-pin" class="w-4 h-4" />
                                </span>
                                Interactive Maps, Eircode & GPS Radius Search
                            </div>
                            <p class="text-xs text-stone-600 leading-relaxed">
                                Expand county filtering with interactive Mapbox or Google Maps, automatic Irish Eircode validation, and spatial GPS radius calculations to find providers within 5 km, 15 km, or 30 km of the customer.
                            </p>
                        </div>

                        <div class="p-5 rounded-2xl bg-white border border-stone-200 shadow-xs hover:border-teal-300 transition-colors">
                            <div class="flex items-center gap-2.5 mb-2 font-bold text-stone-900">
                                <span class="p-2 rounded-xl bg-teal-50 text-teal-700 border border-teal-100">
                                    <x-ui.icon name="calendar" class="w-4 h-4" />
                                </span>
                                Two-Way Calendar Sync & Custom Job Bids
                            </div>
                            <p class="text-xs text-stone-600 leading-relaxed">
                                Synchronize provider availability with Google Calendar and Apple iCal via standard iCal/CalDAV feeds to prevent schedule collisions, and allow customers to request custom price quotes for non-standard jobs.
                            </p>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
</x-layouts.app>

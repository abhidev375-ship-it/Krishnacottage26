@extends('layouts.customer')

@section('title', 'Reservation Confirmed | ' . $reservation->booking_code)

@section('content')
<div class="mx-auto max-w-[860px] px-4 sm:px-6 py-6 sm:py-10">

    <!-- SUCCESS ANIMATION / ICON -->
    <div class="text-center space-y-3 mb-5 sm:mb-8">
        <div class="w-16 h-16 rounded-2xl bg-mint text-emerald flex items-center justify-center mx-auto soft-border shadow-card">
            <i data-lucide="check-circle-2" class="w-10 h-10"></i>
        </div>
        <span class="eyebrow text-brass block">Reservation Confirmed</span>
        <h1 class="serif text-3xl sm:text-4xl font-bold text-forest">Your Stay Awaits</h1>
        <p class="text-xs sm:text-sm text-forest/60 max-w-md mx-auto">
            We have confirmed your reservation. A confirmation itinerary has also been dispatched to <strong>{{ $reservation->guest ? $reservation->guest->email : 'your email' }}</strong>.
        </p>
    </div>

    <!-- DIGITAL STAY PASS CARD -->
    <div class="bg-white rounded-[28px] soft-border shadow-card overflow-hidden">
        <!-- TOP EMBLEM STRIP -->
        <div class="bg-forest text-paper px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-white/10 text-brass font-bold text-xs">K</span>
                <span class="serif text-sm font-bold tracking-wider">Krishna Resorts Stay Pass</span>
            </div>
            <span class="text-[10px] bg-white/10 px-3 py-1 rounded-full font-bold uppercase text-brass">
                {{ ucfirst($reservation->status) }}
            </span>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <!-- BOOKING REFERENCE CODE & QR CODE -->
            <div class="bg-mint/50 rounded-2xl p-5 border border-emerald/20 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="text-[9px] uppercase font-bold text-forest/50 tracking-wider">Booking Reference</span>
                    <div class="font-mono text-2xl sm:text-3xl font-bold text-forest tracking-wider mt-0.5">
                        {{ $reservation->booking_code }}
                    </div>
                    <div class="text-xs text-forest/60 mt-1">
                        Reserved for: <strong class="text-forest">{{ $reservation->guest ? $reservation->guest->full_name : 'Guest' }}</strong>
                    </div>
                </div>

                <!-- SIMULATED DIGITAL QR CODE FOR INSTANT CHECK-IN -->
                <div class="bg-white p-2.5 rounded-2xl soft-border text-center shrink-0 shadow-xs">
                    <div class="w-24 h-24 bg-forest rounded-xl flex items-center justify-center text-paper text-[10px] font-mono p-2">
                        <div class="grid grid-cols-4 gap-1 w-full h-full opacity-90">
                            <div class="bg-paper col-span-2 row-span-2 rounded-xs"></div>
                            <div class="bg-paper col-span-1"></div>
                            <div class="bg-paper col-span-1"></div>
                            <div class="bg-paper col-span-1"></div>
                            <div class="bg-paper col-span-2 row-span-2 rounded-xs"></div>
                            <div class="bg-paper col-span-1"></div>
                        </div>
                    </div>
                    <span class="text-[9px] text-forest/50 font-semibold block mt-1">Scan at Gate</span>
                </div>
            </div>

            <!-- STAY ITINERARY DETAILS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-b border-forest/10 pb-6">
                <div>
                    <h3 class="eyebrow text-forest/50 mb-1">Room & Branch Destination</h3>
                    <div class="serif text-lg font-bold text-forest">{{ $reservation->roomType ? $reservation->roomType->name : 'Signature Suite' }}</div>
                    <div class="text-xs text-emerald font-semibold mt-0.5">{{ $reservation->branch ? $reservation->branch->name : 'Krishna Resort' }}</div>
                    <div class="text-xs text-forest/70 mt-1.5 flex items-start gap-1.5">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-brass shrink-0 mt-0.5"></i>
                        <span>{{ $reservation->branch ? $reservation->branch->full_address : 'Kerala, India' }}</span>
                    </div>
                    @php
                        $confPhone = $reservation->branch?->phone ?? \App\Models\Setting::get('resort_phone', '+91 94471 22334');
                        $cleanConfPhone = preg_replace('/[^0-9+]/', '', $confPhone);
                    @endphp
                    @if($confPhone)
                        <div class="mt-2.5 flex items-center gap-2">
                            <a href="tel:{{ $cleanConfPhone }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold hover:bg-emerald-100 transition shadow-xs" title="Call Front Desk">
                                <i data-lucide="phone-call" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                                <span>Call Front Desk: {{ $confPhone }}</span>
                            </a>
                        </div>
                    @endif
                    @if($reservation->branch)
                        <a href="{{ $reservation->branch->google_maps_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald hover:underline mt-2">
                            <span>Get Driving Directions on Map</span>
                            <i data-lucide="external-link" class="w-3 h-3"></i>
                        </a>
                    @endif
                </div>

                <div>
                    <h3 class="eyebrow text-forest/50 mb-1">Assigned Suite Unit</h3>
                    <div class="text-sm font-bold text-forest">
                        @if($reservation->room)
                            Villa Unit {{ $reservation->room->room_number }} (Floor {{ $reservation->room->floor ?? 'G' }})
                        @else
                            Assigned Upon Front Desk Arrival
                        @endif
                    </div>
                    <div class="text-xs text-forest/50 mt-0.5">
                        {{ $reservation->adults }} Adult{{ $reservation->adults > 1 ? 's' : '' }} &middot; 1 Room &middot; Authentic Nature Veranda
                    </div>
                </div>
            </div>

            <!-- DATES TIMELINE -->
            <div class="grid grid-cols-2 gap-4 border-b border-forest/10 pb-6">
                <div>
                    <span class="text-[9px] uppercase font-bold text-forest/50">Check-in</span>
                    <div class="text-sm font-bold text-forest mt-0.5">
                        {{ $reservation->check_in_date ? date('l, M d, Y', strtotime($reservation->check_in_date)) : 'Upcoming' }}
                    </div>
                    <div class="text-xs text-emerald font-semibold">From 2:00 PM</div>
                </div>

                <div>
                    <span class="text-[9px] uppercase font-bold text-forest/50">Check-out</span>
                    <div class="text-sm font-bold text-forest mt-0.5">
                        {{ $reservation->check_out_date ? date('l, M d, Y', strtotime($reservation->check_out_date)) : 'Departure' }}
                    </div>
                    <div class="text-xs text-forest/50">Until 11:00 AM</div>
                </div>
            </div>

            <!-- PAYMENT STATUS RECEIPT -->
            <div class="space-y-2 border-b border-forest/10 pb-6 text-xs text-forest/70">
                <h3 class="eyebrow text-forest/50 mb-2">Payment Details</h3>
                <div class="flex justify-between">
                    <span>Rate ({{ $reservation->nights }} nights)</span>
                    <span class="font-semibold text-forest">₹{{ number_format($reservation->subtotal_amount) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Taxes & GST</span>
                    <span class="font-semibold text-forest">₹{{ number_format($reservation->tax_amount) }}</span>
                </div>
                <div class="pt-2 border-t border-forest/5 flex justify-between font-bold text-sm text-forest">
                    <span>Total Amount</span>
                    <span>₹{{ number_format($reservation->total_amount) }}</span>
                </div>
                <div class="flex justify-between text-xs pt-1">
                    <span>Paid Today:</span>
                    <span class="text-emerald font-bold">₹{{ number_format($reservation->paid_amount) }}</span>
                </div>
                @if($reservation->total_amount > $reservation->paid_amount)
                <div class="flex justify-between text-xs text-amber-800 font-semibold bg-paper p-2 rounded-xl soft-border">
                    <span>Balance Due Upon Arrival:</span>
                    <span>₹{{ number_format($reservation->total_amount - $reservation->paid_amount) }}</span>
                </div>
                @endif
            </div>

            <!-- ACTIONS -->
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <a href="{{ route('customer.account') }}" class="flex-1 py-3 px-4 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs text-center shadow-card transition">
                    View My Bookings
                </a>
                <button onclick="window.print()" class="py-3 px-5 rounded-xl soft-border bg-paper hover:bg-white text-forest font-bold text-xs flex items-center justify-center gap-2 transition">
                    <i data-lucide="printer" class="w-4 h-4 text-emerald"></i>
                    <span>Print Stay Pass</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

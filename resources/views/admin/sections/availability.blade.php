<!-- BACKWARD COMPATIBILITY ALIAS FOR AVAILABILITY (Integrated into Accommodations Hub) -->
<section id=availability class=section space-y-5>
    <div class=p-8 text-center bg-white rounded-2xl border border-gray-200 shadow-xs space-y-3>
        <div class=w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto>
            <i data-lucide=grid-3x3 class=w-6 h-6></i>
        </div>
        <h3 class=text-base font-bold text-brand-text>Room Availability Matrix</h3>
        <p class=text-xs text-brand-muted max-w-md mx-auto>The 7-Day availability calendar has been unified into the <strong>Accommodations & Rooms Hub</strong>.</p>
        <button onclick=navigateTo('rooms'); switchRoomTab('availability'); class=px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-xs transition inline-flex items-center gap-1.5 touch-tap>
            <span>Open Availability Matrix</span>
            <i data-lucide=arrow-right class=w-4 h-4></i>
        </button>
    </div>
</section>

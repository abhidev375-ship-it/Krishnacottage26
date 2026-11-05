<!-- BACKWARD COMPATIBILITY ALIAS FOR ROOM STATUS (Integrated into Accommodations Hub) -->
<section id=room-status class=section space-y-5>
    <div class=p-8 text-center bg-white rounded-2xl border border-gray-200 shadow-xs space-y-3>
        <div class=w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto>
            <i data-lucide=clipboard-check class=w-6 h-6></i>
        </div>
        <h3 class=text-base font-bold text-brand-text>Room Housekeeping & Status Board</h3>
        <p class=text-xs text-brand-muted max-w-md mx-auto>This board has been unified into the <strong>Accommodations & Rooms Hub</strong> for fast 1-tap floorplan housekeeping management.</p>
        <button onclick=navigateTo('rooms'); switchRoomTab('floorplan'); class=px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-xs transition inline-flex items-center gap-1.5 touch-tap>
            <span>Open Room Status Board</span>
            <i data-lucide=arrow-right class=w-4 h-4></i>
        </button>
    </div>
</section>

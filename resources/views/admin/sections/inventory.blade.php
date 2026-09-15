<!-- BACKWARD COMPATIBILITY ALIAS FOR INVENTORY (Integrated into Spices Hub) -->
<section id=inventory class=section space-y-5>
    <div class=p-8 text-center bg-white rounded-2xl border border-gray-200 shadow-xs space-y-3>
        <div class=w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto>
            <i data-lucide=package class=w-6 h-6></i>
        </div>
        <h3 class=text-base font-bold text-brand-text>Spice Inventory Ledger</h3>
        <p class=text-xs text-brand-muted max-w-md mx-auto>The stock ledger and restock adjustments have been consolidated into the unified <strong>Krishna Spices Hub</strong>.</p>
        <button onclick=navigateTo('spices'); switchSpiceTab('inventory'); class=px-4 py-2 bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold rounded-xl shadow-xs transition inline-flex items-center gap-1.5 touch-tap>
            <span>Open Inventory Ledger</span>
            <i data-lucide=arrow-right class=w-4 h-4></i>
        </button>
    </div>
</section>

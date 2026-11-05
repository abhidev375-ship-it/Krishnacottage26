<!-- BACKWARD COMPATIBILITY ALIAS FOR FOOD ORDERS (Integrated into Dining Hub) -->
<section id=food-orders class=section space-y-5>
    <div class=p-8 text-center bg-white rounded-2xl border border-gray-200 shadow-xs space-y-3>
        <div class=w-12 h-12 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center mx-auto>
            <i data-lucide=chef-hat class=w-6 h-6></i>
        </div>
        <h3 class=text-base font-bold text-brand-text>Kitchen & Dining Orders (KOT)</h3>
        <p class=text-xs text-brand-muted max-w-md mx-auto>The Kitchen Kanban board has been consolidated into the unified <strong>Plantation Dining & Kitchen Hub</strong>.</p>
        <button onclick=navigateTo('dining'); switchDiningTab('orders'); class=px-4 py-2 bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold rounded-xl shadow-xs transition inline-flex items-center gap-1.5 touch-tap>
            <span>Open Kitchen Orders Board</span>
            <i data-lucide=arrow-right class=w-4 h-4></i>
        </button>
    </div>
</section>

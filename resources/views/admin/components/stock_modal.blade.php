<!-- SPICE STOCK ADJUSTMENT MODAL -->
<div id="stock-adjust-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-brand-text/50 backdrop-blur-xs transition-opacity" onclick="closeStockModal()"></div>

    <div class="relative w-full max-w-md max-h-[92vh] bg-brand-surface rounded-2xl shadow-2xl overflow-hidden flex flex-col border border-gray-200 z-10">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/80 shrink-0">
            <div>
                <h3 class="text-base font-bold text-brand-text">Adjust Product Stock</h3>
                <p class="text-xs text-brand-muted" id="stock-modal-product-name">Product Name</p>
            </div>
            <button onclick="closeStockModal()" class="p-1.5 hover:bg-gray-200 rounded-lg text-gray-500 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="stock-adjust-form" onsubmit="submitStockAdjustment(event)" class="p-4 sm:p-5 space-y-4 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="stock-modal-product-id" name="product_id">
            
            <div class="p-3 bg-brand-canvas rounded-lg flex justify-between items-center">
                <span class="text-brand-muted">Current Stock on Hand:</span>
                <span class="font-extrabold text-sm text-brand-text" id="stock-modal-current-qty">0</span>
            </div>

            <div class="space-y-1">
                <label class="font-bold text-brand-text block">Adjustment Type</label>
                <select name="adjustment_type" required class="w-full p-2 bg-brand-canvas border border-gray-200 rounded-lg">
                    <option value="stock_in">Stock In (Received Shipment / Harvest)</option>
                    <option value="stock_out">Stock Out (Manual Issue / Sale)</option>
                    <option value="correction">Inventory Audit Correction</option>
                    <option value="damaged">Damaged / Expired</option>
                    <option value="return">Customer Return Restock</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="font-bold text-brand-text block">Quantity Change (+ or -)</label>
                <input type="number" name="quantity_change" placeholder="e.g. 10 or -5" required class="w-full p-2 bg-brand-canvas border border-gray-200 rounded-lg font-bold">
            </div>

            <div class="space-y-1">
                <label class="font-bold text-brand-text block">Audit Reason Note</label>
                <textarea name="reason" rows="2" placeholder="Required for compliance and traceability" required class="w-full p-2 bg-brand-canvas border border-gray-200 rounded-lg"></textarea>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeStockModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text font-semibold rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-brand-primary hover:bg-brand-deep text-white font-bold rounded-lg shadow-sm transition">
                    Save Adjustment
                </button>
            </div>
        </form>
    </div>
</div>

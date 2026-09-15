<!-- LUXURY PAGE-TRANSITION ANIMATED LOADER (98% Background Seenable, 2% Blur) -->
<div id="page-transition-loader" class="fixed inset-0 z-[99999] flex flex-col items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200 ease-out" style="background: rgba(244, 241, 232, 0.08); backdrop-filter: blur(1.5px); -webkit-backdrop-filter: blur(1.5px);">
    <div class="relative flex flex-col items-center select-none transform transition-transform duration-200 scale-95" id="page-loader-spinner-box">
        <!-- Outer Luxury Ring Spinner -->
        <div class="relative w-14 h-14 sm:w-16 sm:h-16 flex items-center justify-center">
            <div class="absolute inset-0 rounded-full border-[2.5px] border-forest/10 border-t-brass animate-spin"></div>
            <div class="absolute inset-1.5 rounded-full border border-forest/5"></div>
            
            <!-- Central Monogram Crest -->
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-forest text-paper shadow-md flex items-center justify-center font-serif text-base sm:text-lg font-bold border border-brass/30">
                <span>K</span>
            </div>
            
            <!-- Subtle Orbiting Ping Dot -->
            <span class="absolute top-0 right-1.5 h-2 w-2 rounded-full bg-brass animate-ping opacity-75"></span>
        </div>

        <!-- Micro Brand Label -->
        <div class="mt-3 text-center">
            <span class="text-[9.5px] uppercase tracking-[0.28em] font-bold text-forest/85 block">Krishna</span>
            <span class="text-[8px] uppercase tracking-[0.22em] font-semibold text-forest/60 block mt-0.5">Cottage</span>
        </div>
    </div>
</div>

<script>
    (function() {
        const loader = document.getElementById('page-transition-loader');
        const spinnerBox = document.getElementById('page-loader-spinner-box');
        let loaderTimeout = null;

        function showPageLoader() {
            if (!loader) return;
            loader.classList.remove('opacity-0', 'pointer-events-none');
            loader.classList.add('opacity-100', 'pointer-events-auto');
            if (spinnerBox) {
                spinnerBox.classList.remove('scale-95');
                spinnerBox.classList.add('scale-100');
            }
            // Auto safety timeout (never trap the user if navigation is aborted or slow)
            clearTimeout(loaderTimeout);
            loaderTimeout = setTimeout(hidePageLoader, 4500);
        }

        function hidePageLoader() {
            if (!loader) return;
            loader.classList.remove('opacity-100', 'pointer-events-auto');
            loader.classList.add('opacity-0', 'pointer-events-none');
            if (spinnerBox) {
                spinnerBox.classList.remove('scale-100');
                spinnerBox.classList.add('scale-95');
            }
            clearTimeout(loaderTimeout);
        }

        // Attach to all internal navigation links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');
            if (!href) return;

            // Ignore hash links, javascript, tel, mailto
            if (href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('tel:') || href.startsWith('mailto:')) {
                return;
            }

            // Ignore target="_blank" or download links
            if (link.target === '_blank' || link.hasAttribute('download')) {
                return;
            }

            // Ignore modifier keys (Cmd/Ctrl click to open new tab)
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) {
                return;
            }

            // Check domain: only trigger for same-origin internal links
            try {
                const url = new URL(link.href, window.location.origin);
                if (url.origin !== window.location.origin) {
                    return;
                }
                // If it points to the exact same URL including hash, do not show
                if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) {
                    return;
                }
            } catch (err) {
                return;
            }

            showPageLoader();
        });

        // Trigger on standard GET search/filter form submissions
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form && form.method && form.method.toLowerCase() === 'get') {
                showPageLoader();
            }
        });

        // Immediately hide loader when page loads or comes from bfcache (back/forward)
        window.addEventListener('pageshow', function(e) {
            hidePageLoader();
        });
        window.addEventListener('load', hidePageLoader);
        document.addEventListener('DOMContentLoaded', hidePageLoader);

        // Expose globally if needed
        window.showPageLoader = showPageLoader;
        window.hidePageLoader = hidePageLoader;
    })();
</script>

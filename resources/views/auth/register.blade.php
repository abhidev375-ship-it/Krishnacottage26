<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title>Create Guest Account — Krishna Cottages</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    :root { color-scheme: light; }
    body {
      font-family: Inter, ui-sans-serif, system-ui, sans-serif;
      color: #17312A;
      background: #FAF8F5;
    }
    .serif { font-family: Georgia, "Times New Roman", serif; }
    .eyebrow { font-size: 10.5px; letter-spacing: .22em; text-transform: uppercase; font-weight: 700; }
  </style>
</head>
<body class="h-full flex flex-col bg-[#FAF8F5] text-forest selection:bg-brass selection:text-forest antialiased overflow-x-hidden">
  @php
    $loginPageImage = \App\Models\Setting::get('login_page_image', 'https://images.unsplash.com/photo-1580618672591-eb180b1a973f?auto=format&fit=crop&w=1600&q=80');
  @endphp

  <!-- Single Viewport Container -->
  <div class="min-h-screen lg:h-screen lg:max-h-screen flex flex-col lg:grid lg:grid-cols-12 bg-[#FAF8F5] lg:overflow-hidden">
    <!-- Left Hero Image Column (Single Viewport Fitted) -->
    <aside class="relative hidden lg:flex lg:col-span-5 xl:col-span-5 flex-col justify-between p-7 xl:p-9 overflow-hidden bg-forest text-paper h-full">
      <!-- High-res Resort / Backwaters / Wooden Cottage Imagery -->
      <img 
        src="{{ $loginPageImage }}" 
        alt="Krishna Cottages Kerala Slow Living" 
        class="absolute inset-0 h-full w-full object-cover object-center scale-105 transition duration-1000 ease-out"
      />
      <!-- Luxury Dark Overlay Gradient -->
      <div class="absolute inset-0 bg-gradient-to-t from-[#083F34] via-[#083F34]/55 to-[#083F34]/35 backdrop-blur-[0.5px]"></div>
      
      <!-- Top Brand Bar -->
      <div class="relative z-10 flex items-center justify-between">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 group">
          <div class="grid h-9 w-9 place-items-center rounded-xl bg-brass text-forest font-black text-sm shadow-md group-hover:scale-105 transition">
            K
          </div>
          <div>
            <span class="block text-xs font-bold tracking-wider uppercase text-paper leading-tight">Krishna Cottages</span>
            <span class="block text-[9px] text-paper/70 tracking-widest uppercase">Kerala Slow Living</span>
          </div>
        </a>

        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 hover:bg-white/20 text-[11px] font-semibold text-paper/90 backdrop-blur-md transition">
          <i data-lucide="arrow-left" class="w-3 h-3"></i>
          <span>Cottage Home</span>
        </a>
      </div>

      <!-- Center / Bottom Luxury Feature & Perks Glass Card -->
      <div class="relative z-10 space-y-3.5 xl:space-y-4">
        <div class="space-y-1.5">
          <span class="inline-block px-2.5 py-0.5 rounded-full bg-brass/25 border border-brass/40 text-[9px] uppercase tracking-widest font-bold text-brass">
            Bespoke Hospitality
          </span>
          <h2 class="serif text-2xl xl:text-3xl font-bold leading-tight text-white drop-shadow-xs">
            Awaken to morning mist & private wooden cottages.
          </h2>
          <p class="text-xs text-paper/85 leading-relaxed max-w-sm font-light">
            Reserve your stay across Kumarakom backwaters, Munnar hills, and Wayanad rainforests with slow-living care.
          </p>
        </div>

        <!-- Compact Glass Perks Box -->
        <div class="rounded-2xl bg-white/10 border border-white/15 p-3.5 backdrop-blur-md space-y-2 shadow-xl text-xs text-paper/90">
          <div class="flex items-center gap-2.5">
            <div class="grid h-6 w-6 shrink-0 place-items-center rounded-lg bg-brass/20 text-brass">
              <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
            </div>
            <span><strong>Direct Guest Rate</strong> &middot; Guaranteed best pricing on all suites</span>
          </div>
          <div class="flex items-center gap-2.5">
            <div class="grid h-6 w-6 shrink-0 place-items-center rounded-lg bg-brass/20 text-brass">
              <i data-lucide="utensils" class="w-3.5 h-3.5"></i>
            </div>
            <span><strong>Clay-Pot Dining</strong> &middot; Pre-order authentic plantation dishes</span>
          </div>
          <div class="flex items-center gap-2.5">
            <div class="grid h-6 w-6 shrink-0 place-items-center rounded-lg bg-brass/20 text-brass">
              <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
            </div>
            <span><strong>Zero Pre-payment Lock</strong> &middot; Instant confirmation & flexibility</span>
          </div>
        </div>

        <!-- Rating Pill -->
        <div class="flex items-center gap-2.5 pt-0.5">
          <div class="flex text-amber-300">
            <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-300"></i>
            <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-300"></i>
            <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-300"></i>
            <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-300"></i>
            <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-300"></i>
          </div>
          <span class="text-[11px] font-semibold text-paper/90">4.95 / 5 &middot; Loved by 1,200+ Guests</span>
        </div>
      </div>
    </aside>

    <!-- Right Form Column (Single Viewport Fitted, Perfectly Breathable) -->
    <main class="col-span-12 lg:col-span-7 xl:col-span-7 flex flex-col justify-between p-6 sm:p-7 lg:p-7 xl:p-9 h-full overflow-y-auto lg:overflow-hidden">
      <!-- Mobile Top Navigation (Visible on mobile screens) -->
      <div class="flex lg:hidden items-center justify-between pb-3.5 border-b border-forest/10 mb-3 shrink-0">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
          <div class="grid h-8 w-8 place-items-center rounded-xl bg-forest text-paper font-black text-xs">K</div>
          <span class="text-xs font-bold uppercase tracking-wider text-forest">Krishna Cottages</span>
        </a>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-xs font-bold text-forest/70 hover:text-forest transition">
          <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
          <span>Back</span>
        </a>
      </div>

      <!-- Center Form Wrapper (Vertically Balanced without any scroll on desktop) -->
      <div class="w-full max-w-lg mx-auto my-auto space-y-3.5 xl:space-y-4">
        <!-- Header -->
        <div class="space-y-1">
          <div class="flex items-center gap-2">
            <span class="eyebrow text-emerald font-bold">New Guest Registration</span>
            <span class="h-1 w-1 rounded-full bg-emerald/40"></span>
            <span class="text-[11px] text-forest/50 font-medium">Takes less than 1 minute</span>
          </div>
          <h1 class="serif text-2xl sm:text-3xl font-bold text-forest tracking-tight">Create Your Guest Account</h1>
          <p class="text-xs sm:text-[13px] text-forest/65 font-normal">
            Register to reserve cottages, slow-living wellness therapies, and estate dining.
          </p>
        </div>

        <!-- Google Fast Auth Button (Clean, Spacious, Top Level) -->
        <div class="space-y-2.5">
          <a href="{{ route('auth.google') }}" class="w-full py-2.5 px-5 rounded-xl border border-forest/15 bg-white hover:bg-forest/5 hover:border-forest/30 transition-all flex items-center justify-center gap-3 text-xs sm:text-sm font-bold text-forest shadow-xs hover:shadow-md group cursor-pointer">
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
              <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
              <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.14-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
              <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
            </svg>
            <span class="group-hover:translate-x-0.5 transition">Sign up with Google</span>
          </a>

          <!-- Elegant Divider -->
          <div class="relative flex items-center justify-center">
            <div class="border-t border-forest/12 w-full"></div>
            <span class="bg-[#FAF8F5] px-3 text-[10px] font-bold uppercase tracking-widest text-forest/40">or register with details</span>
            <div class="border-t border-forest/12 w-full"></div>
          </div>
        </div>

        <!-- Session / Errors Notices -->
        @if(session('google_notice'))
          <div class="rounded-xl bg-brass/15 border border-brass/30 p-2.5 flex items-center gap-2 text-xs text-forest font-medium">
            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-brass shrink-0"></i>
            <span>{{ session('google_notice') }}</span>
          </div>
        @endif

        @if($errors->any())
          <div class="rounded-xl bg-red-50 border border-red-200 p-2.5 flex items-start gap-2 text-xs text-red-800">
            <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5 text-red-600"></i>
            <div class="space-y-0.5">
              @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
              @endforeach
            </div>
          </div>
        @endif

        <!-- Spacious, Compact-Fitted Form -->
        <form action="{{ route('register.submit') }}" method="POST" class="space-y-2.5">
          @csrf

          <!-- Full Name -->
          <div class="space-y-1">
            <label for="nameInput" class="block text-[11px] font-bold uppercase tracking-wider text-forest/70">Full Name</label>
            <div class="relative flex items-center">
              <i data-lucide="user" class="w-3.5 h-3.5 text-forest/40 absolute left-3.5 pointer-events-none"></i>
              <input 
                type="text" 
                name="name" 
                id="nameInput" 
                value="{{ old('name') }}" 
                placeholder="e.g. Ananya Nair" 
                required 
                autofocus
                class="w-full bg-white border border-forest/15 rounded-xl pl-9 pr-3.5 py-2 text-xs sm:text-[13px] font-medium text-forest placeholder:text-forest/35 focus:outline-hidden focus:border-forest focus:ring-2 focus:ring-forest/10 transition shadow-2xs"
              />
            </div>
          </div>

          <!-- Email & Phone Grid (2 Columns) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <!-- Email -->
            <div class="space-y-1">
              <label for="emailInput" class="block text-[11px] font-bold uppercase tracking-wider text-forest/70">Email Address</label>
              <div class="relative flex items-center">
                <i data-lucide="mail" class="w-3.5 h-3.5 text-forest/40 absolute left-3.5 pointer-events-none"></i>
                <input 
                  type="email" 
                  name="email" 
                  id="emailInput" 
                  value="{{ old('email') }}" 
                  placeholder="ananya@domain.com" 
                  required 
                  class="w-full bg-white border border-forest/15 rounded-xl pl-9 pr-3.5 py-2 text-xs sm:text-[13px] font-medium text-forest placeholder:text-forest/35 focus:outline-hidden focus:border-forest focus:ring-2 focus:ring-forest/10 transition shadow-2xs"
                />
              </div>
            </div>

            <!-- Phone -->
            <div class="space-y-1">
              <label for="phoneInput" class="block text-[11px] font-bold uppercase tracking-wider text-forest/70">Phone (Mobile)</label>
              <div class="relative flex items-center">
                <i data-lucide="phone" class="w-3.5 h-3.5 text-forest/40 absolute left-3.5 pointer-events-none"></i>
                <input 
                  type="tel" 
                  name="phone" 
                  id="phoneInput" 
                  value="{{ old('phone') }}" 
                  placeholder="+91 98450 11223" 
                  required 
                  class="w-full bg-white border border-forest/15 rounded-xl pl-9 pr-3.5 py-2 text-xs sm:text-[13px] font-medium text-forest placeholder:text-forest/35 focus:outline-hidden focus:border-forest focus:ring-2 focus:ring-forest/10 transition shadow-2xs"
                />
              </div>
            </div>
          </div>

          <!-- Passwords Grid (2 Columns) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <!-- Password -->
            <div class="space-y-1">
              <label for="regPassword" class="block text-[11px] font-bold uppercase tracking-wider text-forest/70">Password</label>
              <div class="relative flex items-center">
                <i data-lucide="lock" class="w-3.5 h-3.5 text-forest/40 absolute left-3.5 pointer-events-none"></i>
                <input 
                  type="password" 
                  name="password" 
                  id="regPassword" 
                  placeholder="Min. 8 characters" 
                  required 
                  class="w-full bg-white border border-forest/15 rounded-xl pl-9 pr-9 py-2 text-xs sm:text-[13px] font-medium text-forest placeholder:text-forest/35 focus:outline-hidden focus:border-forest focus:ring-2 focus:ring-forest/10 transition shadow-2xs"
                />
                <button type="button" onclick="togglePasswordVisibility('regPassword', 'pwdRegToggleIcon')" class="absolute right-2.5 text-forest/40 hover:text-forest transition cursor-pointer" title="Toggle visibility">
                  <i data-lucide="eye" id="pwdRegToggleIcon" class="w-3.5 h-3.5"></i>
                </button>
              </div>
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1">
              <label for="regPasswordConfirm" class="block text-[11px] font-bold uppercase tracking-wider text-forest/70">Confirm Password</label>
              <div class="relative flex items-center">
                <i data-lucide="lock-keyhole" class="w-3.5 h-3.5 text-forest/40 absolute left-3.5 pointer-events-none"></i>
                <input 
                  type="password" 
                  name="password_confirmation" 
                  id="regPasswordConfirm" 
                  placeholder="Repeat password" 
                  required 
                  class="w-full bg-white border border-forest/15 rounded-xl pl-9 pr-9 py-2 text-xs sm:text-[13px] font-medium text-forest placeholder:text-forest/35 focus:outline-hidden focus:border-forest focus:ring-2 focus:ring-forest/10 transition shadow-2xs"
                />
                <button type="button" onclick="togglePasswordVisibility('regPasswordConfirm', 'pwdRegConfirmToggleIcon')" class="absolute right-2.5 text-forest/40 hover:text-forest transition cursor-pointer" title="Toggle visibility">
                  <i data-lucide="eye" id="pwdRegConfirmToggleIcon" class="w-3.5 h-3.5"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Policy Note -->
          <div class="text-[10.5px] text-forest/55 pt-0.5 leading-snug">
            By registering, you agree to Krishna Cottages' slow-living guest policies and stay charter.
          </div>

          <!-- Submit Button -->
          <button type="submit" class="w-full py-2.5 sm:py-3 px-5 rounded-xl bg-forest hover:bg-forest/90 text-paper font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition cursor-pointer">
            <span>Complete Registration</span>
            <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-brass"></i>
          </button>
        </form>

        <!-- Footer Sign-in Link -->
        <div class="text-center text-xs text-forest/70 pt-0.5">
          <span>Already have a customer account?</span>
          <a href="{{ route('login') }}" class="font-bold text-forest hover:text-emerald underline underline-offset-4 ml-1 transition">Sign In</a>
        </div>
      </div>

      <!-- Bottom Brand Footer -->
      <div class="text-center text-[10.5px] text-forest/40 font-medium shrink-0 pt-2">
        &copy; {{ date('Y') }} Krishna Cottages &middot; Preserving Kerala Heritage & Nature
      </div>
    </main>
  </div>

  <script>
    function togglePasswordVisibility(inputId, iconId) {
      const input = document.getElementById(inputId);
      const icon = document.getElementById(iconId);
      if (!input) return;
      if (input.type === 'password') {
        input.type = 'text';
        if (icon) icon.setAttribute('data-lucide', 'eye-off');
      } else {
        input.type = 'password';
        if (icon) icon.setAttribute('data-lucide', 'eye');
      }
      if (window.lucide) lucide.createIcons();
    }

    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) lucide.createIcons();
    });
  </script>
</body>
</html>

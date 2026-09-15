<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title>Sign In to Account — Krishna Resorts</title>
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
    $loginPageImage = \App\Models\Setting::get('login_page_image', 'https://images.unsplash.com/photo-1590050752117-238cb0fb12b1?auto=format&fit=crop&w=1600&q=80');
  @endphp

  <!-- Single Viewport Container -->
  <div class="min-h-screen lg:h-screen lg:max-h-screen flex flex-col lg:grid lg:grid-cols-12 bg-[#FAF8F5] lg:overflow-hidden">
    <!-- Left Hero Image Column (Single Viewport Fitted) -->
    <aside class="relative hidden lg:flex lg:col-span-5 xl:col-span-5 flex-col justify-between p-7 xl:p-9 overflow-hidden bg-forest text-paper h-full">
      <!-- High-res Kerala Resort Sunset & Waterscape Imagery -->
      <img 
        src="{{ $loginPageImage }}" 
        alt="Krishna Resorts Kerala Slow Living" 
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
            <span class="block text-xs font-bold tracking-wider uppercase text-paper leading-tight">Krishna Resorts</span>
            <span class="block text-[9px] text-paper/70 tracking-widest uppercase">Kerala Slow Living</span>
          </div>
        </a>

        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 hover:bg-white/20 text-[11px] font-semibold text-paper/90 backdrop-blur-md transition">
          <i data-lucide="arrow-left" class="w-3 h-3"></i>
          <span>Resort Home</span>
        </a>
      </div>

      <!-- Center / Bottom Luxury Feature & Perks Glass Card -->
      <div class="relative z-10 space-y-3.5 xl:space-y-4">
        <div class="space-y-1.5">
          <span class="inline-block px-2.5 py-0.5 rounded-full bg-brass/25 border border-brass/40 text-[9px] uppercase tracking-widest font-bold text-brass">
            Guest Portal
          </span>
          <h2 class="serif text-2xl xl:text-3xl font-bold leading-tight text-white drop-shadow-xs">
            Where timeless Kerala serenity welcomes you back.
          </h2>
          <p class="text-xs text-paper/85 leading-relaxed max-w-sm font-light">
            Sign in to manage your private cottage bookings, personalized culinary requests, and tailored estate experiences.
          </p>
        </div>

        <!-- Compact Glass Perks Box -->
        <div class="rounded-2xl bg-white/10 border border-white/15 p-3.5 backdrop-blur-md space-y-2 shadow-xl text-xs text-paper/90">
          <div class="flex items-center gap-2.5">
            <div class="grid h-6 w-6 shrink-0 place-items-center rounded-lg bg-brass/20 text-brass">
              <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
            </div>
            <span><strong>Active Itineraries</strong> &middot; Real-time suite dates, check-in & directions</span>
          </div>
          <div class="flex items-center gap-2.5">
            <div class="grid h-6 w-6 shrink-0 place-items-center rounded-lg bg-brass/20 text-brass">
              <i data-lucide="utensils" class="w-3.5 h-3.5"></i>
            </div>
            <span><strong>Clay-Pot Dining</strong> &middot; Pre-order chef specials to your veranda</span>
          </div>
          <div class="flex items-center gap-2.5">
            <div class="grid h-6 w-6 shrink-0 place-items-center rounded-lg bg-brass/20 text-brass">
              <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
            </div>
            <span><strong>Estate Spice Boutique</strong> &middot; Track your harvested spice orders</span>
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
          <span class="text-xs font-bold uppercase tracking-wider text-forest">Krishna Resorts</span>
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
            <span class="eyebrow text-emerald font-bold">Customer Portal</span>
            <span class="h-1 w-1 rounded-full bg-emerald/40"></span>
            <span class="text-[11px] text-forest/50 font-medium">Welcome Back</span>
          </div>
          <h1 class="serif text-2xl sm:text-3xl font-bold text-forest tracking-tight">Sign In to Your Account</h1>
          <p class="text-xs sm:text-[13px] text-forest/65 font-normal">
            Access your reservations, view room details, and order clay-pot plantation dining.
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
            <span class="group-hover:translate-x-0.5 transition">Continue with Google</span>
          </a>

          <!-- Elegant Divider -->
          <div class="relative flex items-center justify-center">
            <div class="border-t border-forest/12 w-full"></div>
            <span class="bg-[#FAF8F5] px-3 text-[10px] font-bold uppercase tracking-widest text-forest/40">or with phone / email</span>
            <div class="border-t border-forest/12 w-full"></div>
          </div>
        </div>

        <!-- Session / Errors Notices -->
        @if(session('info'))
          <div class="rounded-xl bg-emerald/10 border border-emerald/20 p-2.5 flex items-center gap-2 text-xs text-emerald font-medium">
            <i data-lucide="info" class="w-3.5 h-3.5 shrink-0"></i>
            <span>{{ session('info') }}</span>
          </div>
        @endif

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
        <form action="{{ route('login.submit') }}" method="POST" class="space-y-3">
          @csrf

          <!-- Email or Phone Field -->
          <div class="space-y-1">
            <label for="loginInput" class="block text-[11px] font-bold uppercase tracking-wider text-forest/70">Phone Number or Email</label>
            <div class="relative flex items-center">
              <i data-lucide="user" class="w-3.5 h-3.5 text-forest/40 absolute left-3.5 pointer-events-none"></i>
              <input 
                type="text" 
                name="login" 
                id="loginInput" 
                value="{{ old('login') }}" 
                placeholder="e.g. +91 98450 11223 or guest@domain.com" 
                required 
                autofocus
                class="w-full bg-white border border-forest/15 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-[13px] font-medium text-forest placeholder:text-forest/35 focus:outline-hidden focus:border-forest focus:ring-2 focus:ring-forest/10 transition shadow-2xs"
              />
            </div>
          </div>

          <!-- Password Field -->
          <div class="space-y-1">
            <div class="flex items-center justify-between">
              <label for="passwordInput" class="block text-[11px] font-bold uppercase tracking-wider text-forest/70">Password</label>
            </div>
            <div class="relative flex items-center">
              <i data-lucide="lock" class="w-3.5 h-3.5 text-forest/40 absolute left-3.5 pointer-events-none"></i>
              <input 
                type="password" 
                name="password" 
                id="passwordInput" 
                placeholder="Enter your password" 
                required 
                class="w-full bg-white border border-forest/15 rounded-xl pl-9 pr-9 py-2.5 text-xs sm:text-[13px] font-medium text-forest placeholder:text-forest/35 focus:outline-hidden focus:border-forest focus:ring-2 focus:ring-forest/10 transition shadow-2xs"
              />
              <button type="button" onclick="togglePasswordVisibility('passwordInput', 'pwdToggleIcon')" class="absolute right-2.5 text-forest/40 hover:text-forest transition cursor-pointer" title="Toggle visibility">
                <i data-lucide="eye" id="pwdToggleIcon" class="w-3.5 h-3.5"></i>
              </button>
            </div>
          </div>

          <!-- Remember Me -->
          <div class="flex items-center justify-between pt-0.5 text-xs">
            <label class="inline-flex items-center gap-2 cursor-pointer text-forest/75 font-medium">
              <input type="checkbox" name="remember" value="1" class="w-3.5 h-3.5 rounded-sm border-forest/20 text-forest focus:ring-emerald cursor-pointer" />
              <span>Remember this device</span>
            </label>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="w-full py-2.5 sm:py-3 px-5 rounded-xl bg-forest hover:bg-forest/90 text-paper font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition cursor-pointer">
            <span>Sign In to Account</span>
            <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-brass"></i>
          </button>
        </form>

        <!-- Footer Sign-up Link -->
        <div class="text-center text-xs text-forest/70 pt-0.5">
          <span>Don't have an account yet?</span>
          <a href="{{ route('register') }}" class="font-bold text-forest hover:text-emerald underline underline-offset-4 ml-1 transition">Create an account</a>
        </div>
      </div>

      <!-- Bottom Brand Footer -->
      <div class="text-center text-[10.5px] text-forest/40 font-medium shrink-0 pt-2">
        &copy; {{ date('Y') }} Krishna Resorts &middot; Preserving Kerala Heritage & Nature
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

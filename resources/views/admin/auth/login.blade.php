<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Staff & Administration Login — Krishna Resorts</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    body {
      background: #04251F;
      background-image: radial-gradient(circle at 10% 20%, rgba(11,93,75,.4), transparent 40%), radial-gradient(circle at 90% 80%, rgba(199,167,106,.15), transparent 40%);
      font-family: Inter, ui-sans-serif, system-ui, sans-serif;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 text-white selection:bg-[#C7A76A] selection:text-[#063F34]">
  <div class="w-full max-w-md">
    <!-- Brand Center Badge -->
    <div class="text-center mb-8 space-y-2">
      <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#0B5D4B] border border-white/10 shadow-lg mb-2">
        <i data-lucide="shield-check" class="w-7 h-7 text-[#C7A76A]"></i>
      </div>
      <h1 class="text-2xl font-bold tracking-tight text-white">Management Console</h1>
      <p class="text-xs text-white/50">Krishna Resorts Operations & Central Admin</p>
    </div>

    <!-- Login Card -->
    <div class="bg-[#063F34]/90 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl space-y-6">
      @if(session('info'))
        <div class="rounded-xl bg-white/5 border border-white/10 p-3 text-xs text-white/80 flex items-center gap-2">
          <i data-lucide="info" class="w-4 h-4 text-[#C7A76A] shrink-0"></i>
          <span>{{ session('info') }}</span>
        </div>
      @endif

      @if(session('error'))
        <div class="rounded-xl bg-red-500/10 border border-red-500/20 p-3 text-xs text-red-300 flex items-center gap-2">
          <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
          <span>{{ session('error') }}</span>
        </div>
      @endif

      @if($errors->any())
        <div class="rounded-xl bg-red-500/10 border border-red-500/20 p-3 text-xs text-red-300 space-y-1">
          @foreach($errors->all() as $err)
            <div class="flex items-center gap-2">
              <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
              <span>{{ $err }}</span>
            </div>
          @endforeach
        </div>
      @endif

      <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
        @csrf

        <!-- Email -->
        <div class="space-y-1.5">
          <label class="block text-[11px] font-bold uppercase tracking-wider text-white/60">Staff Email</label>
          <div class="relative flex items-center">
            <i data-lucide="mail" class="w-4 h-4 text-white/40 absolute left-3.5"></i>
            <input 
              type="email" 
              name="email" 
              value="{{ old('email', 'admin@krishnaresorts.com') }}" 
              required 
              autofocus 
              placeholder="admin@krishnaresorts.com" 
              class="w-full bg-[#04251F]/70 border border-white/15 rounded-xl pl-10 pr-4 py-3 text-xs font-semibold text-white placeholder:text-white/30 focus:outline-hidden focus:border-[#C7A76A] transition"
            />
          </div>
        </div>

        <!-- Password -->
        <div class="space-y-1.5">
          <label class="block text-[11px] font-bold uppercase tracking-wider text-white/60">Password</label>
          <div class="relative flex items-center">
            <i data-lucide="lock" class="w-4 h-4 text-white/40 absolute left-3.5"></i>
            <input 
              type="password" 
              name="password" 
              value="password123" 
              required 
              placeholder="••••••••" 
              class="w-full bg-[#04251F]/70 border border-white/15 rounded-xl pl-10 pr-4 py-3 text-xs font-semibold text-white placeholder:text-white/30 focus:outline-hidden focus:border-[#C7A76A] transition"
            />
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-[#C7A76A] hover:bg-[#b5955a] text-[#063F34] font-bold text-xs flex items-center justify-center gap-2 shadow-lg transition cursor-pointer hover:shadow-xl hover:-translate-y-0.5 mt-2">
          <span>Sign In to Dashboard</span>
          <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </button>
      </form>

      <!-- Security Notice -->
      <div class="pt-2 text-center text-[10px] text-white/40 border-t border-white/10">
        <p>Restricted to authorized personnel. All management activities are logged with IP and audit trail.</p>
      </div>
    </div>

    <!-- Back to Client site -->
    <div class="mt-6 text-center">
      <a href="{{ route('home') }}" class="text-xs text-white/50 hover:text-white transition inline-flex items-center gap-1.5">
        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
        <span>Return to Customer Platform</span>
      </a>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      lucide.createIcons();
    });
  </script>
</body>
</html>

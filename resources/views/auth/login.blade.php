<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login — JDIH Kota Kendari</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}">

  <!-- Font sama dengan home: Source Sans 3 -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

  @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-stone-100 text-stone-800 antialiased font-opensans">
  <div class="grid min-h-screen lg:grid-cols-[1.05fr_1fr]">

    {{-- Brand panel --}}
    <aside class="relative hidden overflow-hidden bg-cover bg-center p-12 text-white lg:flex lg:flex-col lg:justify-between"
           style="background-image:linear-gradient(150deg, rgba(20,14,13,.86) 0%, rgba(120,30,22,.72) 55%, rgba(160,45,32,.6) 100%), url('{{ asset('assets/img/bg-login.jpg') }}');">
      <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/80">
        Jaringan Dokumentasi &amp; Informasi Hukum
      </div>

      <div>
        <h1 class="mb-4 text-[44px] font-bold leading-[1.08] tracking-tight">
          Dashboard Admin<br><span class="text-red-200">JDIH Kota Kendari</span>
        </h1>
        <p class="max-w-[42ch] text-[15px] leading-[1.7] text-white/80">
          Kelola dokumen hukum, peraturan, putusan, dan publikasi dengan akses yang cepat, mudah, dan terkini.
        </p>
        <div class="mt-7 flex flex-wrap gap-2.5">
          @foreach ([
            ['Akses Cepat', '<polyline points="20 6 9 17 4 12"/>'],
            ['Terkini', '<circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 14"/>'],
            ['Terpercaya', '<path d="M12 3l7 4v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V7z"/>'],
          ] as [$label, $icon])
            <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-[12.5px] font-medium backdrop-blur-sm">
              <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $icon !!}</svg>
              {{ $label }}
            </span>
          @endforeach
        </div>
      </div>

      <div class="text-xs text-white/60">© {{ date('Y') }} Pemerintah Kota Kendari</div>
    </aside>

    {{-- Form panel --}}
    <main class="flex items-center justify-center px-6 py-10">
      <div class="w-full max-w-[400px]">
        <a href="{{ route('frontend.beranda') }}" class="mb-7 block">
          <img src="{{ asset('assets/img/jdih-logo.png') }}" alt="Logo JDIH Kota Kendari" class="h-[52px] w-auto">
        </a>
        <h2 class="mb-1.5 text-[23px] font-bold tracking-tight">Masuk ke Dashboard</h2>
        <p class="mb-7 text-[13.5px] text-stone-500">Silakan masuk menggunakan akun admin Anda.</p>

        @if ($errors->any())
          <div class="mb-5 flex items-start gap-2.5 rounded-lg border border-red-200 bg-red-50 px-3.5 py-3 text-[13px] leading-relaxed text-red-800">
            <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ $errors->first() }}</span>
          </div>
        @endif

        <form action="{{ route('authenticate') }}" method="POST" novalidate class="space-y-4">
          @csrf

          <div>
            <label for="username" class="mb-1.5 block text-[12.5px] font-semibold text-stone-500">Username</label>
            <div class="relative">
              <svg class="pointer-events-none absolute left-3.5 top-1/2 h-[17px] w-[17px] -translate-y-1/2 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <input type="text" id="username" name="username" placeholder="Masukkan username" required autofocus autocomplete="username" value="{{ old('username') }}"
                     class="h-[46px] w-full rounded-lg border border-stone-300 bg-white pl-10 pr-3.5 text-sm outline-none transition placeholder:text-stone-400 focus:border-red-600 focus:ring-[3px] focus:ring-red-600/15">
            </div>
            @error('username')<small class="mt-1.5 block text-xs text-red-600">{{ $message }}</small>@enderror
          </div>

          <div>
            <label for="password" class="mb-1.5 block text-[12.5px] font-semibold text-stone-500">Password</label>
            <div class="relative">
              <svg class="pointer-events-none absolute left-3.5 top-1/2 h-[17px] w-[17px] -translate-y-1/2 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <input type="password" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password"
                     class="h-[46px] w-full rounded-lg border border-stone-300 bg-white pl-10 pr-11 text-sm outline-none transition placeholder:text-stone-400 focus:border-red-600 focus:ring-[3px] focus:ring-red-600/15">
              <button type="button" id="togglePw" aria-label="Tampilkan password"
                      class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md p-2 text-stone-400 transition hover:bg-stone-100 hover:text-stone-800">
                <svg id="eyeOpen" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg id="eyeOff" class="hidden h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.5 18.5 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              </button>
            </div>
            @error('password')<small class="mt-1.5 block text-xs text-red-600">{{ $message }}</small>@enderror
          </div>

          <label class="flex cursor-pointer select-none items-center gap-2 text-[13px] text-stone-500">
            <input type="checkbox" name="rememberMe" value="1" {{ old('rememberMe') ? 'checked' : '' }}
                   class="h-[18px] w-[18px] rounded border-stone-300 text-red-600 accent-red-600 focus:ring-red-600/20">
            Ingat saya
          </label>

          <button type="submit"
                  class="flex h-[47px] w-full items-center justify-center gap-2.5 rounded-lg bg-red-600 text-[14.5px] font-semibold text-white shadow-lg shadow-red-600/25 transition hover:bg-red-700 hover:shadow-red-600/30 active:translate-y-px">
            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            Masuk
          </button>
        </form>

        <a href="{{ route('frontend.beranda') }}" class="mt-5 block text-center text-[13px] text-stone-500 transition hover:text-red-600">← Kembali ke situs utama</a>
      </div>
    </main>
  </div>

  <script>
    (function () {
      var btn = document.getElementById('togglePw');
      var pw = document.getElementById('password');
      var on = document.getElementById('eyeOpen');
      var off = document.getElementById('eyeOff');
      btn.addEventListener('click', function () {
        var show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        on.classList.toggle('hidden', show);
        off.classList.toggle('hidden', !show);
        btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
      });
    })();
  </script>
</body>

</html>

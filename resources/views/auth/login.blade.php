<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Gudang Labkesda</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font Poppins --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* ✅ Button Login Effect */
        .btn-login {
            transition: all 0.2s ease;
        }

        /* ✅ Hover Glow */
        .btn-login:hover {
            filter: brightness(1.08);
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.35);
        }

        /* ✅ Press Effect + Text Black */
        .btn-login:active {
            transform: scale(0.96);
            box-shadow: 0 3px 10px rgba(56, 189, 248, 0.25);

            /* ✅ Text berubah hitam saat ditekan */
            color: black;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-950 flex items-center justify-center px-4 py-8 relative overflow-hidden">

    {{-- Background Blur Aura --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-gradient-to-br 
                    from-indigo-500 via-sky-500 to-cyan-400 
                    opacity-20 blur-3xl rounded-full"></div>

        <div class="absolute -bottom-52 -right-36 w-96 h-96 bg-gradient-to-tr 
                    from-emerald-400 via-blue-500 to-indigo-500 
                    opacity-15 blur-3xl rounded-full"></div>
    </div>

    {{-- CARD LOGIN --}}
    <div class="relative z-10 w-full max-w-md sm:max-w-lg 
                rounded-3xl backdrop-blur-xl 
                bg-slate-950/70 border border-slate-800 shadow-2xl 
                px-7 sm:px-10 py-8 sm:py-9">

        {{-- HEADER --}}
        <div class="flex flex-col items-center text-center mb-7">

            {{-- LOGO --}}
            <img src="{{ asset('img/logo.png') }}"
                 alt="Logo Labkesda"
                 class="h-16 w-16 sm:h-20 sm:w-20 object-contain drop-shadow-lg">

            {{-- INSTANSI --}}
            <p class="mt-3 text-[11px] sm:text-xs font-semibold 
                      text-slate-300 uppercase tracking-widest">
                UPTD LABKESDA KABUPATEN CIREBON
            </p>

            {{-- NAMA APLIKASI --}}
            <h1 class="mt-2 text-xl sm:text-2xl font-bold text-white leading-snug">
                Gudang Reagen & BHP
                <span class="block text-cyan-400 font-semibold">
                    Metode FEFO
                </span>
            </h1>

            {{-- TAGLINE --}}
            <p class="mt-2 text-sm text-slate-400 leading-relaxed max-w-sm">
                Sistem Pengendalian Stok Reagen dan BHP Berbasis Web
            </p>
        </div>


        {{-- FORM LOGIN --}}
        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            {{-- EMAIL --}}
            <div>
                <label class="text-sm font-medium text-slate-200">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    placeholder="kepala@labkesda.com"
                    class="mt-2 w-full rounded-xl px-4 py-3 
                           bg-slate-900 border border-slate-700
                           text-white placeholder-slate-500 
                           focus:ring-2 focus:ring-sky-400 
                           focus:outline-none transition"
                >
            </div>

            {{-- PASSWORD --}}
            <div>
                <label class="text-sm font-medium text-slate-200">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    required
                    placeholder="••••••••"
                    class="mt-2 w-full rounded-xl px-4 py-3 
                           bg-slate-900 border border-slate-700
                           text-white placeholder-slate-500 
                           focus:ring-2 focus:ring-sky-400 
                           focus:outline-none transition"
                >
            </div>

            {{-- REMEMBER --}}
            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="remember"
                       class="h-4 w-4 rounded border-slate-600 
                              text-sky-400 bg-slate-900">
                <span class="text-sm text-slate-300">
                    Ingat saya di perangkat ini
                </span>
            </div>

            {{-- ✅ BUTTON LOGIN --}}
            <button
                type="submit"
                class="btn-login w-full mt-3 py-3 rounded-xl 
                       bg-gradient-to-r from-indigo-500 via-sky-500 to-cyan-400
                       text-white font-semibold shadow-lg 
                       shadow-sky-500/30">
                Login
            </button>
        </form>


        {{-- FOOTER --}}
        <p class="mt-7 text-center text-[11px] sm:text-xs text-slate-500 leading-relaxed">
            © {{ date('Y') }} Gudang Reagen & BHP Metode FEFO <br>
            UPTD Labkesda Kabupaten Cirebon
        </p>

    </div>

</body>
</html>

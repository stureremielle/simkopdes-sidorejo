<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Panel Admin - Kop. Merah Putih Sidorejo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        emerald: { 500: '#DC2626', 600: '#B91C1C', 900: '#7F1D1D', 950: '#450a0a' },
                        navy: { 900: '#0c2340' }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-attachment: fixed; }
        .glass-card { background: rgba(255, 255, 255, 0.92); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.6); }
        @keyframes float-slow { 0%, 100% { transform: translateY(0px) scale(1); } 50% { transform: translateY(-20px) scale(1.05); } }
        .animate-float { animation: float-slow 8s ease-in-out infinite; }
        .animate-float-delayed { animation: float-slow 10s ease-in-out infinite 2s; }
        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .animate-slide-in { animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</head>
<body class="bg-[#7F1D1D] min-h-screen flex items-center justify-center relative px-4 py-12">

    <!-- Wadah Kartu Login -->
    <div class="bg-white rounded-[32px] p-8 sm:p-10 shadow-2xl w-full max-w-md relative z-10 text-center transition-all duration-300">
        
        <!-- Blok Ikon Logo -->
        <div class="inline-flex items-center justify-center w-16 h-16 bg-[#FFF1F2] text-[#DC2626] rounded-2xl mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827a1.125 1.125 0 0 1 .26 1.43l-1.297 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.354-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.43l1.004-.827c.292-.24.437-.613.43-.991a6.936 6.936 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
        </div>

        <!-- Informasi Header -->
        <h1 class="text-2xl font-bold text-gray-900 mb-1.5">Panel Admin</h1>
        <p class="text-xs font-semibold text-gray-500 mb-8">Koperasi Desa Merah Putih Sidorejo</p>

        <!-- Pesan Kesalahan -->
        @error('login_error')
        <div class="animate-slide-in flex items-center p-4 rounded-xl border text-sm font-semibold mb-6 bg-red-50 border-red-100 text-red-800">
            <svg class="w-5 h-5 mr-3 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div class="flex-grow text-left">{{ $message }}</div>
        </div>
        @enderror

        <!-- Formulir -->
        <form method="POST" action="{{ route('login') }}" class="text-left space-y-6">
            @csrf
            <!-- Input Nama Pengguna -->
            <div class="space-y-2">
                <label for="username" class="block text-sm font-bold text-gray-700 tracking-wide">Username</label>
                <div class="relative">
                    <input type="text" id="username" name="username" placeholder="admin" required maxlength="20" value="{{ old('username') }}"
                        class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:border-[#DC2626] focus:bg-white focus:ring-4 focus:ring-[#DC2626]/10 text-gray-700 transition-all placeholder-gray-400 font-medium">
                </div>
            </div>

            <!-- Input Kata Sandi -->
            <div class="space-y-2">
                <label for="password" class="block text-sm font-bold text-gray-700 tracking-wide">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="••••••••" required
                        class="w-full px-4 py-3 pr-12 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:border-[#DC2626] focus:bg-white focus:ring-4 focus:ring-[#DC2626]/10 text-gray-700 transition-all placeholder-gray-400 font-medium">
                    <button type="button" id="togglePassword"
                        onclick="togglePasswordVisibility()"
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-[#DC2626] transition-colors duration-200"
                        aria-label="Tampilkan password">
                        <!-- Ikon mata terbuka (ditampilkan saat kata sandi tersembunyi) -->
                        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <!-- Ikon mata tertutup (ditampilkan saat kata sandi terlihat) -->
                        <svg id="eyeSlash" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 hidden">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Tombol Kirim Login -->
            <button type="submit" id="btnSubmit" 
                class="w-full bg-[#B91C1C] hover:bg-[#991B1B] text-white font-bold py-3.5 rounded-2xl shadow-lg transition-all duration-200 mt-2 flex items-center justify-center">
                <span>Masuk</span>
            </button>
        </form>

    </div>
    <script>
        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeSlash = document.getElementById('eyeSlash');
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeSlash.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeSlash.classList.add('hidden');
            }
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Compta+ C+ - Connexion</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 antialiased">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-6xl bg-slate-800/50 backdrop-blur-xl rounded-3xl shadow-2xl border border-slate-700/50 overflow-hidden">
            <div class="grid lg:grid-cols-5">
                <!-- Left Side - Branding (2 cols) -->
                <div class="lg:col-span-2 bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 p-8 flex flex-col justify-between text-white relative overflow-hidden">
                    <!-- Animated background -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-0 -left-4 w-72 h-72 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl animate-blob"></div>
                        <div class="absolute top-0 -right-4 w-72 h-72 bg-purple-400 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-2000"></div>
                        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-400 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-4000"></div>
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="bg-white text-blue-600 font-bold text-xl px-4 py-2 rounded-xl tracking-tight shadow-lg">C+</div>
                            <div class="text-xl font-bold">Compta+ C+</div>
                        </div>
                        <h1 class="text-3xl lg:text-4xl font-bold tracking-tight mb-3">Gérez votre trésorerie en toute confiance</h1>
                        <p class="text-blue-100 text-base">Suivi en temps réel, multi-devises et rapports puissants</p>
                    </div>

                    <div class="space-y-4 relative z-10">
                        <div class="flex items-start gap-3 bg-white/10 backdrop-blur-sm rounded-xl p-3">
                            <div class="p-2 bg-blue-500 rounded-lg shrink-0">
                                <span class="iconify" data-icon="lucide:check" style="width: 18px; height: 18px;"></span>
                            </div>
                            <div>
                                <div class="font-semibold text-sm">Multi-Devises</div>
                                <div class="text-xs text-blue-100">USD, EUR, CDF en temps réel</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 bg-white/10 backdrop-blur-sm rounded-xl p-3">
                            <div class="p-2 bg-blue-500 rounded-lg shrink-0">
                                <span class="iconify" data-icon="lucide:shield-check" style="width: 18px; height: 18px;"></span>
                            </div>
                            <div>
                                <div class="font-semibold text-sm">Sécurisé</div>
                                <div class="text-xs text-blue-100">Données cryptées</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Login Form (3 cols) -->
                <div class="lg:col-span-3 p-8 bg-slate-800">
                    <div class="max-w-md mx-auto">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold tracking-tight text-white mb-1">Bon retour</h2>
                            <p class="text-slate-400 text-sm">Connectez-vous à votre compte</p>
                        </div>

                        @if (session('status'))
                            <div class="mb-4 p-3 bg-emerald-500/20 border border-emerald-500/50 rounded-lg text-sm text-emerald-300">
                                {{ session('status') }}
                            </div>
                        @endif

                        <!-- Google Login -->
                        <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-3 px-4 py-2.5 bg-white hover:bg-gray-50 rounded-xl text-sm font-medium transition-all duration-200 mb-4 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                                <path d="M19.8055 10.2292C19.8055 9.55156 19.7501 8.86719 19.6323 8.19531H10.2002V12.0492H15.6014C15.3773 13.2911 14.6571 14.3898 13.6053 15.0875V17.5867H16.8251C18.7175 15.8449 19.8055 13.2727 19.8055 10.2292Z" fill="#4285F4"/>
                                <path d="M10.2002 20.0008C12.9527 20.0008 15.2643 19.1152 16.8251 17.5867L13.6053 15.0875C12.7029 15.6977 11.5427 16.0438 10.2002 16.0438C7.54382 16.0438 5.29274 14.2828 4.49927 11.9102H1.18359V14.4828C2.78072 17.6563 6.33668 20.0008 10.2002 20.0008Z" fill="#34A853"/>
                                <path d="M4.49927 11.9102C4.04927 10.6683 4.04927 9.33333 4.49927 8.09141V5.51875H1.18359C-0.0890625 7.86719 -0.0890625 10.1336 1.18359 12.482L4.49927 11.9102Z" fill="#FBBC04"/>
                                <path d="M10.2002 3.95703C11.6212 3.93359 13.0006 4.47266 14.0385 5.45703L16.8944 2.60156C15.1766 0.990625 12.7306 0.0773438 10.2002 0.101562C6.33668 0.101562 2.78072 2.44609 1.18359 5.61953L4.49927 8.19219C5.29274 5.81953 7.54382 3.95703 10.2002 3.95703Z" fill="#EA4335"/>
                            </svg>
                            Continuer avec Google
                        </a>

                        <div class="relative my-4">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-700"></div>
                            </div>
                            <div class="relative flex justify-center text-xs">
                                <span class="px-3 bg-slate-800 text-slate-500">Ou avec email</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('login') }}" class="space-y-3">
                            @csrf

                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-1.5">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nom@entreprise.com" class="w-full px-3 py-2.5 bg-slate-700/50 border border-slate-600 rounded-xl text-sm text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                @error('email')<span class="text-red-400 text-xs mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-1.5">Mot de passe</label>
                                <input type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="w-full px-3 py-2.5 bg-slate-700/50 border border-slate-600 rounded-xl text-sm text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                @error('password')<span class="text-red-400 text-xs mt-1">{{ $message }}</span>@enderror
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                                    <input type="checkbox" name="remember" class="w-4 h-4 border-slate-600 rounded bg-slate-700 text-blue-600 focus:ring-2 focus:ring-blue-500">
                                    <span>Se souvenir</span>
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-blue-400 hover:text-blue-300 font-medium">Mot de passe oublié ?</a>
                                @endif
                            </div>

                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                Se connecter
                            </button>

                            <div class="text-center text-xs text-slate-400 pt-2">
                                Pas de compte ? <a href="{{ route('register') }}" class="text-blue-400 hover:text-blue-300 font-semibold">Essai gratuit</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
    </style>
</body>
</html>

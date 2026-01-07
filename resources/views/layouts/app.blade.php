<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-bind:class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Compta+') }} - @yield('title', 'Gestion de Trésorerie')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 antialiased">
    <div class="min-h-screen flex flex-col">
        @auth
            <!-- Top Header & Navigation -->
            <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 sticky top-0 z-50">
                <!-- Top Row: Logo & User -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <!-- Logo -->
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-600 text-white font-bold text-base px-3 py-1.5 rounded-lg tracking-tight">C+</div>
                            <div class="font-semibold tracking-tight dark:text-white hidden sm:block">Compta+ </div>
                        </div>

                        <!-- Top Header Actions -->
                        <div class="flex items-center gap-3">
                            <!-- Dark Mode Toggle -->
                            <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                <span x-show="!darkMode" class="iconify text-slate-600 dark:text-slate-400" data-icon="lucide:moon" style="width: 20px; height: 20px;"></span>
                                <span x-show="darkMode" class="iconify text-slate-600 dark:text-slate-400" data-icon="lucide:sun" style="width: 20px; height: 20px;"></span>
                            </button>

                            <!-- Notifications -->
                            <button class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors relative">
                                <span class="iconify text-slate-600 dark:text-slate-400" data-icon="lucide:bell" style="width: 20px; height: 20px;"></span>
                                @if($pendingCount = \App\Models\Transaction::pending()->count())
                                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                                @endif
                            </button>

                            <div class="hidden sm:block w-px h-6 bg-slate-200 dark:bg-slate-600 mx-2"></div>

                            <!-- User Menu -->
                            <div class="flex items-center gap-3" x-data="{ open: false }">
                                <div class="text-right hidden sm:block">
                                    <div class="text-sm font-medium">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 capitalize">{{ auth()->user()->role }}</div>
                                </div>
                                <button @click="open = !open" class="w-9 h-9 bg-blue-600 text-white rounded-full flex items-center justify-center font-medium text-sm">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="open" @click.away="open = false" x-transition class="absolute top-14 right-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg py-2 w-48 z-50">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">Profil</a>
                                    <div class="border-t border-slate-200 dark:border-slate-700 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">Déconnexion</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row: Navigation Links -->
                <div class="border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <nav class="flex items-center space-x-1 py-2">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-2 py-2 rounded-lg text-sm font-medium whitespace-nowrap {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <span class="iconify" data-icon="lucide:layout-dashboard" style="width: 18px; height: 18px;"></span>
                                Dashboard
                            </a>

                            <a href="{{ route('operations.index') }}" class="flex items-center gap-2 px-2 py-2 rounded-lg text-sm font-medium whitespace-nowrap {{ request()->routeIs('operations.index', 'operations.create') ? 'bg-blue-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <span class="iconify" data-icon="lucide:arrow-right-left" style="width: 18px; height:18px;"></span>
                                Opérations
                            </a>

                            @if(auth()->user()->canValidateTransactions())
                            <a href="{{ route('operations.validate') }}" class="flex items-center gap-2 px-2 py-2 rounded-lg text-sm font-medium whitespace-nowrap {{ request()->routeIs('operations.validate') ? 'bg-blue-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <span class="iconify" data-icon="lucide:check-circle" style="width: 18px; height: 18px;"></span>
                                Validation
                                @if($pendingCount = \App\Models\Operation::pending()->count())
                                    <span class="ml-auto px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 text-xs font-medium rounded-full">{{ $pendingCount }}</span>
                                @endif
                            </a>
                            @endif

                            <a href="{{ route('beneficiaries.index') }}" class="flex items-center gap-2 px-2 py-2 rounded-lg text-sm font-medium whitespace-nowrap {{ request()->routeIs('beneficiaries.*') ? 'bg-blue-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <span class="iconify" data-icon="lucide:users" style="width: 18px; height: 18px;"></span>
                                Bénéficiaires
                            </a>

                            @if(auth()->user()->canValidateTransactions())
                            <a href="{{ route('cashbook.index') }}" class="flex items-center gap-2 px-2 py-2 rounded-lg text-sm font-medium whitespace-nowrap {{ request()->routeIs('cashbook.*') ? 'bg-blue-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <span class="iconify" data-icon="lucide:book-open" style="width: 18px; height: 18px;"></span>
                                Livre de Caisse
                            </a>
                            @endif



                         <!--    <a href="{{ route('exchange.index') }}" class="flex items-center gap-2 px-2 py-2 rounded-lg text-sm font-medium whitespace-nowrap {{ request()->routeIs('exchange.*') ? 'bg-blue-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <span class="iconify" data-icon="lucide:repeat" style="width: 18px; height: 18px;"></span>
                                Bureau de Change
                            </a> -->

                            @if(auth()->user()->isManager())
                            <div class="relative group" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center gap-2 px-2 py-2 rounded-lg text-sm font-medium whitespace-nowrap {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                    <span class="iconify" data-icon="lucide:bar-chart-3" style="width: 18px; height: 18px;"></span>
                                    Rapports
                                    <span class="iconify" data-icon="lucide:chevron-down" style="width: 12px;"></span>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute top-10 left-0 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg py-2 w-56 z-[60]">
                                    <a href="{{ route('reports.cash-journal') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">Journal de caisse</a>
                                    <a href="{{ route('reports.account-report') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">Rapport par compte</a>
                                    <a href="{{ route('reports.balance') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">Solde à date</a>
                                    <a href="{{ route('reports.profit-loss') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">Résultat simplifié</a>
                                </div>
                            </div>

                            <div class="relative group" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center gap-2 px-2 py-2 rounded-lg text-sm font-medium whitespace-nowrap {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                    <span class="iconify" data-icon="lucide:settings" style="width: 18px; height: 18px;"></span>
                                    Paramètres
                                    <span class="iconify" data-icon="lucide:chevron-down" style="width: 12px;"></span>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute top-10 right-0 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg py-2 w-56 z-[60]">
                                    <a href="{{ route('settings.features') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">Fonctionnalités</a>
                                    <a href="{{ route('settings.users') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">Utilisateurs</a>
                                    <a href="{{ route('accounts.index') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">Comptes</a>
                                    <a href="{{ route('settings.cashboxes') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">Caisses</a>
                                    <a href="{{ route('settings.currency') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">Devise & Taux</a>
                                </div>
                            </div>
                            @endif
                        </nav>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @if(session('success'))
                        <div class="mb-6 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4 flex items-start gap-3">
                            <span class="iconify text-emerald-600 dark:text-emerald-400 mt-0.5" data-icon="lucide:check-circle" style="width: 20px; height: 20px;"></span>
                            <div class="flex-1 text-sm font-medium text-emerald-900 dark:text-emerald-100">{{ session('success') }}</div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-start gap-3">
                            <span class="iconify text-red-600 dark:text-red-400 mt-0.5" data-icon="lucide:x-circle" style="width: 20px; height: 20px;"></span>
                            <div class="flex-1 text-sm font-medium text-red-900 dark:text-red-100">{{ session('error') }}</div>
                        </div>
                    @endif

                    @yield('content')
                    {{ $slot ?? '' }}
                </div>
            </main>
        @else
            <!-- Guest Content -->
            <main class="flex-1">
                @yield('content')
            </main>
        @endauth
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>

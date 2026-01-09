<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-bind:class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.bunny.net https://code.iconify.design; shadow-dom 'self';">

    <title>SuperAdmin - {{ config('app.name', 'Compta+') }}</title>

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
<body class="bg-slate-100 dark:bg-slate-900 text-slate-900 dark:text-slate-100 antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- SuperAdmin Header -->
        <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center gap-3">
                        <div class="bg-indigo-500 text-white font-bold text-base px-3 py-1.5 rounded-lg tracking-tight">SA</div>
                        <div class="font-semibold tracking-tight text-white hidden sm:block">SuperAdmin</div>
                    </div>

                    <!-- Navigation -->
                    <nav class="hidden md:flex space-x-8">
                        <a href="{{ route('superadmin.dashboard') }}" class="text-slate-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('superadmin.dashboard') ? 'bg-slate-800 text-white' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('superadmin.tenants') }}" class="text-slate-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('superadmin.tenants') ? 'bg-slate-800 text-white' : '' }}">
                            Tenants (Organisations)
                        </a>
                        <a href="{{ route('superadmin.users') }}" class="text-slate-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('superadmin.users') ? 'bg-slate-800 text-white' : '' }}">
                            Utilisateurs
                        </a>
                        <a href="{{ route('superadmin.licenses') }}" class="text-slate-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('superadmin.licenses') ? 'bg-slate-800 text-white' : '' }}">
                            Licences
                        </a>
                    </nav>

                    <!-- User Menu -->
                    <div class="flex items-center gap-4">
                        <!-- Dark Mode Toggle -->
                        <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="p-2 hover:bg-slate-800 rounded-lg transition-colors">
                            <span x-show="!darkMode" class="iconify text-slate-400" data-icon="lucide:moon" style="width: 20px; height: 20px;"></span>
                            <span x-show="darkMode" class="iconify text-slate-400" data-icon="lucide:sun" style="width: 20px; height: 20px;"></span>
                        </button>

                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-medium text-white">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-slate-400">Super Administrateur</div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-2 text-slate-400 hover:text-white rounded-lg transition-colors">
                                <span class="iconify" data-icon="lucide:log-out" style="width: 20px; height: 20px;"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                 @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>

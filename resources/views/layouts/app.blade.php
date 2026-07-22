<!DOCTYPE html>
<html lang="sr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — DRNDA ERP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 font-sans antialiased" x-data="{}">
<div class="flex h-full">

    <aside class="hidden md:flex md:flex-col w-60 flex-shrink-0 bg-[#1a1f2e]">
        <div class="flex items-center gap-3 px-5 py-5 border-b border-[#2a3045]">
            <div class="w-8 h-8 rounded-lg bg-yellow-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <div class="text-sm font-bold text-white tracking-wide">DRNDA ERP</div>
                <div class="text-[10px] text-[#8a94b0] uppercase tracking-widest">v3.0</div>
            </div>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            @php
                $nav = [
                    ['route' => 'dashboard',       'label' => 'Dashboard',    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'settings.index',  'label' => 'Podesavanja',  'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                ];
            @endphp
            @foreach($nav as $link)
            @if(Route::has($link['route']))
            <a href="{{ route($link['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                      {{ request()->routeIs(rtrim($link['route'],'.*').'*')
                          ? 'bg-yellow-600/20 text-yellow-400 font-semibold'
                          : 'text-[#8a94b0] hover:bg-[#232840] hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $link['icon'] }}"/>
                </svg>
                {{ $link['label'] }}
            </a>
            @endif
            @endforeach
        </nav>
        <div class="border-t border-[#2a3045] px-4 py-3" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-3 w-full text-left rounded-lg px-1 py-2 hover:bg-[#232840] transition-colors">
                <div class="w-8 h-8 rounded-full bg-yellow-600/30 flex items-center justify-center flex-shrink-0">
                    <span class="text-yellow-400 text-xs font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm text-white font-medium truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-[#8a94b0] truncate">{{ auth()->user()->email }}</div>
                </div>
            </button>
            <div x-show="open" x-cloak @click.outside="open = false" class="mt-1 rounded-lg bg-[#232840] border border-[#2a3045] overflow-hidden">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 w-full px-4 py-2.5 text-sm text-[#8a94b0] hover:bg-[#2a3045] hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Odjavi se
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <header class="flex-shrink-0 bg-white border-b border-gray-200 px-6 py-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">{{ $title ?? 'Dashboard' }}</h1>
                    @isset($subtitle)<p class="text-xs text-gray-500 mt-0.5">{{ $subtitle }}</p>@endisset
                </div>
                @isset($headerActions)<div class="flex items-center gap-3">{{ $headerActions }}</div>@endisset
            </div>
        </header>
        @if(session('success'))
        <div class="mx-6 mt-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-green-800">{{ session('success') }}</p>
        </div>
        @endif
        @if(session('error'))
        <div class="mx-6 mt-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 flex items-center gap-3">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
        @endif
        <main class="flex-1 overflow-y-auto p-6">{{ $slot }}</main>
    </div>
</div>
</body>
</html>

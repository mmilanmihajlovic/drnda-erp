<x-app-layout>
    <x-slot name="title">Podesavanja</x-slot>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @can('manage-users')
        <a href="{{ route('settings.users.index') }}" class="card card-body group hover:border-yellow-200 transition-colors">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center group-hover:bg-yellow-200 transition-colors">
                    <svg class="w-5 h-5 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div><h3 class="font-semibold text-gray-900">Korisnici</h3><p class="text-sm text-gray-500 mt-1">Upravljanje korisnicima i ulogama</p></div>
            </div>
        </a>
        @endcan
        @can('manage-settings')
        <a href="{{ route('settings.workers.index') }}" class="card card-body group hover:border-yellow-200 transition-colors">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                    <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div><h3 class="font-semibold text-gray-900">Radnici</h3><p class="text-sm text-gray-500 mt-1">Evidencija radnika po odeljenjima</p></div>
            </div>
        </a>
        <a href="{{ route('settings.vehicles.index') }}" class="card card-body group hover:border-yellow-200 transition-colors">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <div><h3 class="font-semibold text-gray-900">Vozila</h3><p class="text-sm text-gray-500 mt-1">Vozni park kompanije</p></div>
            </div>
        </a>
        <a href="{{ route('settings.items.index') }}" class="card card-body group hover:border-yellow-200 transition-colors">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                    <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div><h3 class="font-semibold text-gray-900">Artikli i usluge</h3><p class="text-sm text-gray-500 mt-1">Cenovnik i katalog</p></div>
            </div>
        </a>
        @endcan
    </div>
</x-app-layout>

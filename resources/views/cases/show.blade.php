<x-app-layout>
    <x-slot name="title">{{ $case->case_number }} — {{ $case->deceased_name }}</x-slot>
    <x-slot name="headerActions">
        @can('edit-cases')
        <a href="{{ route('cases.edit', $case) }}" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Izmeni
        </a>
        @endcan
    </x-slot>

    {{-- ── Case header ────────────────────────────────────────────────── --}}
    <div class="card mb-5">
        <div class="card-body">
            <div class="flex flex-wrap items-start justify-between gap-4">

                {{-- Meta --}}
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="font-mono text-lg font-bold text-gray-700">{{ $case->case_number }}</span>
                        @if($case->case_type == 'domaci')
                        <span class="status-chip bg-blue-100 text-blue-700">Domaci</span>
                        @else
                        <span class="status-chip bg-purple-100 text-purple-700">Ino</span>
                        @endif
                        @if($case->status == 'aktivan')
                        <span class="status-inprogress">Aktivan</span>
                        @else
                        <span class="status-done">Zatvoren</span>
                        @endif
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $case->deceased_name }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-1 mt-3 text-sm text-gray-600">
                        @if($case->family_contact_name)
                        <div>
                            <span class="text-gray-400 text-xs uppercase tracking-wide block">Porodica</span>
                            {{ $case->family_contact_name }}
                            @if($case->family_contact_phone)
                            <span class="text-gray-400"> · {{ $case->family_contact_phone }}</span>
                            @endif
                        </div>
                        @endif
                        @if($case->display_location)
                        <div>
                            <span class="text-gray-400 text-xs uppercase tracking-wide block">
                                {{ $case->case_type == 'ino' ? 'Relacija' : 'Mesto' }}
                            </span>
                            {{ $case->display_location }}
                        </div>
                        @endif
                        @if($case->funeral_at)
                        <div>
                            <span class="text-gray-400 text-xs uppercase tracking-wide block">Datum i vreme</span>
                            {{ $case->funeral_at->format('d.m.Y H:i') }}
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Overall progress --}}
                <div class="text-center min-w-24">
                    <div class="text-3xl font-bold {{ $overallProgress >= 100 ? 'text-green-600' : 'text-gray-700' }}">
                        {{ $overallProgress }}%
                    </div>
                    <div class="text-xs text-gray-400 mt-1">Ukupno</div>
                    <div class="w-24 h-2 bg-gray-200 rounded-full mt-2 overflow-hidden">
                        <div class="h-full rounded-full transition-all
                            {{ $overallProgress >= 100 ? 'bg-green-500' : ($overallProgress > 0 ? 'bg-yellow-500' : 'bg-gray-300') }}"
                             style="width: {{ $overallProgress }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Department progress chips --}}
            <div class="flex flex-wrap gap-3 mt-5 pt-4 border-t border-gray-100">
                @foreach($case->caseDepartments as $cd)
                <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-50 border border-gray-200">
                    <span class="text-sm font-medium text-gray-700">{{ $cd->department->name }}</span>
                    <div class="w-16 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $cd->progress >= 100 ? 'bg-green-500' : ($cd->progress > 0 ? 'bg-yellow-500' : 'bg-gray-300') }}"
                             style="width: {{ $cd->progress }}%"></div>
                    </div>
                    <span class="text-sm font-semibold {{ $cd->progress >= 100 ? 'text-green-600' : 'text-gray-600' }}">{{ $cd->progress }}%</span>
                    @if($cd->status === 'zatvoreno')
                    <span class="status-done text-xs">Zatvoreno</span>
                    @elseif($cd->status === 'nema_aktivnosti')
                    <span class="status-chip bg-gray-100 text-gray-500 text-xs">N/A</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Tabs ──────────────────────────────────────────────────────── --}}
    <div x-data="{ tab: 'pregled' }">

        {{-- Tab navigation --}}
        <div class="flex gap-1 mb-5 bg-white rounded-xl border border-gray-100 shadow-sm p-1 w-fit">
            @foreach([
                ['key' => 'pregled',   'label' => 'Pregled'],
                ['key' => 'pogrebno',  'label' => 'Pogrebno'],
                ['key' => 'jna',       'label' => 'JNA'],
                ['key' => 'cvecara',   'label' => 'Cvecara'],
            ] as $t)
            <button @click="tab = '{{ $t['key'] }}'"
                    :class="tab === '{{ $t['key'] }}' ? 'bg-yellow-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                {{ $t['label'] }}
            </button>
            @endforeach
        </div>

        {{-- TAB: Pregled --}}
        <div x-show="tab === 'pregled'" x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach($case->caseDepartments as $cd)
                <div class="card">
                    <div class="card-header">
                        <h3 class="font-semibold text-gray-800">{{ $cd->department->name }}</h3>
                        @if($cd->status === 'zatvoreno')
                        <span class="status-done">Zatvoreno</span>
                        @elseif($cd->status === 'nema_aktivnosti')
                        <span class="status-chip bg-gray-100 text-gray-500">N/A</span>
                        @else
                        <span class="status-inprogress">Aktivan</span>
                        @endif
                    </div>
                    <div class="card-body border-t border-gray-100">
                        {{-- Progress bar --}}
                        <div class="mb-3">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Napredak</span>
                                <span class="font-semibold {{ $cd->progress >= 100 ? 'text-green-600' : 'text-gray-700' }}">{{ $cd->progress }}%</span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all
                                    {{ $cd->progress >= 100 ? 'bg-green-500' : ($cd->progress > 0 ? 'bg-yellow-500' : 'bg-gray-300') }}"
                                     style="width: {{ $cd->progress }}%"></div>
                            </div>
                        </div>

                        @if($cd->closed_at)
                        <div class="text-xs text-gray-500">
                            Zatvoreno: {{ $cd->closed_at->format('d.m.Y H:i') }}
                            @if($cd->closedBy) · {{ $cd->closedBy->name }} @endif
                        </div>
                        @else
                        <div class="text-xs text-gray-400">Nema aktivnosti jos.</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Case info --}}
            <div class="mt-5 card">
                <div class="card-header">
                    <h3 class="text-sm font-semibold text-gray-700">Informacije o slucaju</h3>
                </div>
                <div class="card-body border-t border-gray-100">
                    <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-400 text-xs uppercase tracking-wide">Kreiran</dt>
                            <dd class="font-medium mt-0.5">{{ $case->created_at->format('d.m.Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-400 text-xs uppercase tracking-wide">Kreirao</dt>
                            <dd class="font-medium mt-0.5">{{ optional($case->createdBy)->name ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- TAB: Pogrebno --}}
        <div x-show="tab === 'pogrebno'" x-cloak>
            <div class="card">
                <div class="card-body text-center py-12">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-gray-500 font-medium">Pogrebno — dolazi u Fazi 3</p>
                    <p class="text-sm text-gray-400 mt-1">Prodaja, zadaci i dokumenta bice dostupni nakon implementacije Faze 3.</p>
                </div>
            </div>
        </div>

        {{-- TAB: JNA --}}
        <div x-show="tab === 'jna'" x-cloak>
            <div class="card">
                <div class="card-body text-center py-12">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-gray-500 font-medium">JNA — dolazi u Fazi 4</p>
                    <p class="text-sm text-gray-400 mt-1">Kanban zadaci, dodela radnika i vozila bice dostupni u Fazi 4.</p>
                </div>
            </div>
        </div>

        {{-- TAB: Cvecara --}}
        <div x-show="tab === 'cvecara'" x-cloak>
            <div class="card">
                <div class="card-body text-center py-12">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <p class="text-gray-500 font-medium">Cvecara — dolazi u Fazi 5</p>
                    <p class="text-sm text-gray-400 mt-1">Prodaja, proizvodnja i isporuka cveca bice dostupni u Fazi 5.</p>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

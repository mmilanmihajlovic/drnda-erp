<x-app-layout>
    <x-slot name="title">Slucajevi</x-slot>
    <x-slot name="subtitle">Lista aktivnih i zatvorenih slucajeva</x-slot>
    <x-slot name="headerActions">
        @can('create-cases')
        <a href="{{ route('cases.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novi slucaj
        </a>
        @endcan
    </x-slot>

    {{-- Filteri --}}
    <form method="GET" action="{{ route('cases.index') }}"
          class="mb-5 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-48">
            <input type="text" name="search"
                   value="{{ request('search') }}"
                   placeholder="Pretraga po imenu, broju slucaja..."
                   class="form-input w-full">
        </div>
        <div>
            <select name="type" class="form-select">
                <option value="">Svi tipovi</option>
                <option value="domaci" {{ request('type') == 'domaci' ? 'selected' : '' }}>Domaci</option>
                <option value="ino"    {{ request('type') == 'ino'    ? 'selected' : '' }}>Ino</option>
            </select>
        </div>
        <div>
            <select name="status" class="form-select">
                <option value="">Svi statusi</option>
                <option value="aktivan"   {{ request('status') == 'aktivan'  ? 'selected' : '' }}>Aktivni</option>
                <option value="zatvoren" {{ request('status') == 'zatvoren' ? 'selected' : '' }}>Zatvoreni</option>
            </select>
        </div>
        <button type="submit" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Filter
        </button>
        @if(request()->hasAny(['search','type','status']))
        <a href="{{ route('cases.index') }}" class="btn-secondary text-gray-500">Ocisti</a>
        @endif
    </form>

    {{-- Tabela --}}
    <div class="card">
        <table class="table-base">
            <thead>
                <tr>
                    <th class="th">Broj</th>
                    <th class="th">Pokojnik</th>
                    <th class="th">Tip</th>
                    <th class="th">Mesto / Relacija</th>
                    <th class="th">Datum</th>
                    <th class="th text-center">Pogrebno</th>
                    <th class="th text-center">JNA</th>
                    <th class="th text-center">Cvecara</th>
                    <th class="th text-center">Ukupno</th>
                    <th class="th w-16"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($cases as $case)
                @php
                    $depts = $case->caseDepartments->keyBy(fn($cd) => $cd->department->code ?? '');
                    $overall = $case->overall_progress;
                @endphp
                <tr class="tr-hover cursor-pointer"
                    onclick="window.location='{{ route('cases.show', $case) }}'">
                    <td class="td">
                        <span class="font-mono text-sm font-semibold text-gray-700">{{ $case->case_number }}</span>
                    </td>
                    <td class="td">
                        <div class="font-medium text-gray-900">{{ $case->deceased_name }}</div>
                        @if($case->family_contact_name)
                        <div class="text-xs text-gray-500">{{ $case->family_contact_name }}</div>
                        @endif
                    </td>
                    <td class="td">
                        @if($case->case_type == 'domaci')
                        <span class="status-chip bg-blue-100 text-blue-700">Domaci</span>
                        @else
                        <span class="status-chip bg-purple-100 text-purple-700">Ino</span>
                        @endif
                    </td>
                    <td class="td text-gray-600 text-sm">{{ $case->display_location ?: '—' }}</td>
                    <td class="td text-sm text-gray-600">
                        {{ $case->funeral_at?->format('d.m.Y') ?? '—' }}
                    </td>
                    {{-- Department statuses --}}
                    @foreach(['pogrebno', 'jna', 'cvecara'] as $code)
                    @php $cd = $depts->get($code) @endphp
                    <td class="td text-center">
                        @if($cd)
                            @if($cd->status === 'zatvoreno')
                            <span class="status-done">{{ $cd->progress }}%</span>
                            @elseif($cd->status === 'nema_aktivnosti')
                            <span class="status-chip bg-gray-100 text-gray-500">N/A</span>
                            @else
                            <span class="status-inprogress">{{ $cd->progress }}%</span>
                            @endif
                        @else
                        <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    @endforeach
                    {{-- Overall progress --}}
                    <td class="td text-center">
                        <div class="inline-flex flex-col items-center gap-1">
                            <span class="text-sm font-semibold {{ $overall >= 100 ? 'text-green-600' : 'text-gray-700' }}">
                                {{ $overall }}%
                            </span>
                            <div class="w-16 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all
                                    {{ $overall >= 100 ? 'bg-green-500' : ($overall > 0 ? 'bg-yellow-500' : 'bg-gray-300') }}"
                                     style="width: {{ $overall }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="td text-right" onclick="event.stopPropagation()">
                        <a href="{{ route('cases.show', $case) }}"
                           class="text-sm text-yellow-600 hover:text-yellow-700 font-medium">
                            Otvori
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="td text-center text-gray-400 py-12">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>Nema slucajeva.</p>
                        @can('create-cases')
                        <a href="{{ route('cases.create') }}" class="text-yellow-600 hover:text-yellow-700 text-sm font-medium mt-1 inline-block">
                            Kreiraj prvi slucaj →
                        </a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($cases->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $cases->links() }}
        </div>
        @endif
    </div>
</x-app-layout>

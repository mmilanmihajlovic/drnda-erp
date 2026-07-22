<x-app-layout>
    <x-slot name="title">Cvecara — Proizvodni Kanban</x-slot>
    <x-slot name="subtitle">Status svih cvecara stavki</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('cvecara.dashboard') }}" class="btn-secondary text-sm">← Dashboard</a>
    </x-slot>

    {{-- Filteri --}}
    <form method="GET" class="flex flex-wrap items-end gap-3 mb-5">
        <select name="worker_id" class="form-select text-sm" onchange="this.form.submit()">
            <option value="">Svi radnici</option>
            @foreach($workers as $w)
            <option value="{{ $w->id }}" {{ request('worker_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
            @endforeach
        </select>
        @if(request('worker_id'))
        <a href="{{ route('cvecara.production') }}" class="btn-secondary text-sm">Ocisti</a>
        @endif
    </form>

    {{-- 5-kolona Kanban --}}
    <div
        x-data="cvecaraKanban()"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3"
        style="min-height:480px"
    >
        @foreach($columns as $statusKey => $col)
        @php
            $colItems = $grouped->get($statusKey, collect());
            $colorMap = [
                'gray'   => ['bg' => 'bg-gray-50',    'border' => 'border-gray-200', 'dot' => 'bg-gray-400',  'head' => 'text-gray-600',  'badge' => 'bg-gray-200 text-gray-700'],
                'blue'   => ['bg' => 'bg-blue-50',    'border' => 'border-blue-100', 'dot' => 'bg-blue-500',  'head' => 'text-blue-700',  'badge' => 'bg-blue-100 text-blue-700'],
                'yellow' => ['bg' => 'bg-yellow-50',  'border' => 'border-yellow-100','dot' => 'bg-yellow-400','head' => 'text-yellow-700','badge' => 'bg-yellow-100 text-yellow-800'],
                'purple' => ['bg' => 'bg-purple-50',  'border' => 'border-purple-100','dot' => 'bg-purple-500','head' => 'text-purple-700','badge' => 'bg-purple-100 text-purple-700'],
                'green'  => ['bg' => 'bg-green-50',   'border' => 'border-green-100', 'dot' => 'bg-green-500', 'head' => 'text-green-700', 'badge' => 'bg-green-100 text-green-700'],
            ];
            $c = $colorMap[$col['color']];
        @endphp
        <div
            class="rounded-xl border {{ $c['border'] }} {{ $c['bg'] }} flex flex-col"
            @dragover.prevent="dragOver('{{ $statusKey }}')"
            @drop.prevent="drop('{{ $statusKey }}')"
            :class="{ 'ring-2 ring-yellow-400 ring-offset-1': dragTarget === '{{ $statusKey }}' }"
        >
            <div class="px-3 py-2.5 border-b {{ $c['border'] }} flex items-center gap-2">
                <div class="w-2 h-2 rounded-full {{ $c['dot'] }}"></div>
                <span class="text-xs font-bold {{ $c['head'] }} uppercase tracking-wide">{{ $col['label'] }}</span>
                <span class="ml-auto text-xs font-semibold px-2 py-0.5 rounded-full {{ $c['badge'] }}">
                    {{ $colItems->count() }}
                </span>
            </div>

            <div class="p-2 flex-1 space-y-2 overflow-y-auto" style="max-height:580px">
                @forelse($colItems as $item)
                <div
                    draggable="true"
                    @dragstart="dragStart({{ $item->id }})"
                    @dragend="dragEnd()"
                    class="bg-white rounded-lg border border-gray-200 p-3 cursor-grab active:cursor-grabbing shadow-sm hover:shadow-md transition-all
                           {{ $item->isLate() ? 'border-l-4 border-l-red-400' : '' }}"
                    :class="{ 'opacity-50 scale-95': dragging === {{ $item->id }} }"
                >
                    @if($item->isLate())
                    <span class="status-error text-xs mb-1.5 block">Kasni</span>
                    @endif

                    <p class="font-semibold text-sm text-gray-900 leading-tight mb-1.5">
                        {{ $item->description }}
                    </p>

                    <div class="flex items-center gap-1 mb-1.5">
                        <span class="font-mono text-xs text-gray-400 font-semibold">{{ $item->flowerOrder->order_number }}</span>
                        @if($item->flowerOrder->funeralCase)
                        <span class="text-gray-300">·</span>
                        <span class="font-mono text-xs text-gray-500">{{ $item->flowerOrder->funeralCase->case_number }}</span>
                        @endif
                    </div>

                    <p class="text-xs text-gray-600 truncate mb-1.5">
                        {{ $item->flowerOrder->customer_name }}
                    </p>

                    @if($item->flowerOrder->delivery_at)
                    <div class="flex items-center gap-1 text-xs {{ $item->isLate() ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $item->flowerOrder->delivery_at->format('d.m H:i') }}
                    </div>
                    @endif

                    @if($item->flowerOrder->delivery_location)
                    <div class="flex items-center gap-1 text-xs text-gray-400 mt-0.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="truncate">{{ $item->flowerOrder->delivery_location }}</span>
                    </div>
                    @endif

                    @if($item->flowerOrder->ribbon_text)
                    <div class="mt-1.5 text-xs text-purple-600 truncate">
                        🎀 {{ Str::limit($item->flowerOrder->ribbon_text, 40) }}
                    </div>
                    @endif

                    <div class="mt-2 pt-2 border-t border-gray-50 text-xs text-gray-500">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ optional($item->assignedWorker)->name ?? 'Nije dodeljeno' }}
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-xs text-gray-300">Prevuci stavku ovde</div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>

    <script>
    function cvecaraKanban() {
        return {
            dragging: null,
            dragTarget: null,
            dragStart(id)     { this.dragging = id; },
            dragEnd()         { this.dragging = null; this.dragTarget = null; },
            dragOver(status)  { this.dragTarget = status; },
            drop(newStatus) {
                if (!this.dragging) return;
                const id = this.dragging;
                this.dragging = null; this.dragTarget = null;
                fetch(`/cvecara/stavke/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status: newStatus })
                }).then(r => r.json()).then(d => { if (d.success) window.location.reload(); })
                  .catch(() => alert('Greška pri promeni statusa.'));
            }
        }
    }
    </script>
</x-app-layout>

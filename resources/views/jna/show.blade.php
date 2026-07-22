<x-app-layout>
    <x-slot name="title">JNA — {{ $case->case_number }}</x-slot>
    <x-slot name="subtitle">{{ $case->deceased_name }}</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('jna.dashboard') }}" class="btn-secondary text-sm">← Kanban</a>
        <a href="{{ route('cases.show', $case) }}" class="btn-secondary text-sm">Slucaj</a>
    </x-slot>

    {{-- Status bar --}}
    <div class="card mb-5">
        <div class="card-body">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <span class="font-mono text-sm font-bold text-gray-600">{{ $case->case_number }}</span>
                    @if($caseDept->status === 'zatvoreno')
                    <span class="status-done">Zatvoreno</span>
                    @elseif($caseDept->status === 'nema_aktivnosti')
                    <span class="status-chip bg-gray-100 text-gray-500">Nema aktivnosti</span>
                    @else
                    <span class="status-inprogress">Aktivno — {{ $caseDept->progress }}%</span>
                    @endif
                </div>
                <div class="flex gap-2">
                    @if($caseDept->isActive())
                    <form method="POST" action="{{ route('jna.close', $case) }}"
                          @submit.prevent="if(confirm('Zatvoriti JNA odeljenje?')) $el.submit()" x-data>
                        @csrf
                        <button class="btn-secondary text-sm">✓ Zatvori JNA</button>
                    </form>
                    <form method="POST" action="{{ route('jna.close', $case) }}"
                          @submit.prevent="if(confirm('Nema aktivnosti za JNA?')) $el.submit()" x-data>
                        @csrf
                        <input type="hidden" name="no_activity" value="1">
                        <button class="btn-secondary text-sm text-gray-400">Nema aktivnosti</button>
                    </form>
                    @else
                    <button onclick="document.getElementById('jna-reopen-form').classList.toggle('hidden')"
                            class="btn-secondary text-sm">↺ Ponovo otvori</button>
                    @endif
                </div>
            </div>

            <div id="jna-reopen-form" class="hidden mt-4 pt-4 border-t border-gray-100">
                <form method="POST" action="{{ route('jna.reopen', $case) }}" class="flex gap-3">
                    @csrf
                    <input name="reason" type="text" required placeholder="Razlog za ponovno otvaranje..."
                           class="form-input flex-1">
                    <button type="submit" class="btn-danger text-sm">Otvori</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Zadaci po statusu --}}
    @php
        $statuses = [
            'novi'     => ['label' => 'Novi',      'class' => 'bg-gray-50 border-gray-200'],
            'dodeljen' => ['label' => 'Dodeljeno', 'class' => 'bg-blue-50 border-blue-100'],
            'u_toku'   => ['label' => 'U toku',    'class' => 'bg-yellow-50 border-yellow-100'],
            'zavrsen'  => ['label' => 'Zavrsen',   'class' => 'bg-green-50 border-green-100'],
        ];
        $grouped = $tasks->groupBy('status');
    @endphp

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach($statuses as $key => $st)
        <div class="rounded-lg border {{ $st['class'] }} px-3 py-2 text-center">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ $st['label'] }}</div>
            <div class="text-2xl font-bold text-gray-800 mt-1">{{ $grouped->get($key, collect())->count() }}</div>
        </div>
        @endforeach
    </div>

    {{-- Lista zadataka --}}
    <div class="card mb-6">
        <div class="card-header">
            <h2 class="text-sm font-semibold text-gray-700">Svi JNA zadaci za ovaj slucaj</h2>
        </div>
        <table class="table-base">
            <thead><tr>
                <th class="th">Zadatak</th>
                <th class="th">Relacija</th>
                <th class="th">Radnik</th>
                <th class="th">Vozilo</th>
                <th class="th">Rok</th>
                <th class="th">Status</th>
                <th class="th w-20"></th>
            </tr></thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($tasks as $task)
                <tr class="tr-hover {{ $task->isLate() ? 'bg-red-50' : '' }}">
                    <td class="td">
                        <div class="font-medium text-sm">{{ $task->title }}</div>
                        @if($task->description)<div class="text-xs text-gray-400 mt-0.5">{{ Str::limit($task->description, 60) }}</div>@endif
                    </td>
                    <td class="td text-xs text-gray-500">
                        @if($task->departure_location || $task->destination)
                        {{ $task->departure_location }}{{ ($task->departure_location && $task->destination) ? ' → ' : '' }}{{ $task->destination }}
                        @else —@endif
                    </td>
                    <td class="td text-sm">{{ optional($task->assignedWorker)->name ?? '—' }}</td>
                    <td class="td text-sm text-gray-500">{{ optional($task->vehicle)->name ?? '—' }}</td>
                    <td class="td text-sm {{ $task->isLate() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                        {{ $task->due_at?->format('d.m.Y H:i') ?? '—' }}
                        @if($task->isLate()) <span class="status-error text-xs ml-1">Kasni</span>@endif
                    </td>
                    <td class="td">
                        @if($caseDept->isActive())
                        <form method="POST" action="{{ route('jna.tasks.update', [$case, $task]) }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="title" value="{{ $task->title }}">
                            <input type="hidden" name="assigned_worker_id" value="{{ $task->assigned_worker_id }}">
                            <input type="hidden" name="vehicle_id" value="{{ $task->vehicle_id }}">
                            <input type="hidden" name="due_at" value="{{ $task->due_at?->format('Y-m-d\TH:i') }}">
                            <input type="hidden" name="departure_location" value="{{ $task->departure_location }}">
                            <input type="hidden" name="destination" value="{{ $task->destination }}">
                            <select name="status" onchange="this.form.submit()"
                                    class="text-xs border border-gray-200 rounded px-2 py-1 focus:border-yellow-500 focus:ring-0 cursor-pointer bg-white">
                                @foreach(['novi' => 'Novi','dodeljen' => 'Dodeljen','u_toku' => 'U toku','zavrsen' => 'Zavrsen'] as $v => $l)
                                <option value="{{ $v }}" {{ $task->status === $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </form>
                        @else
                        <span class="text-xs {{ $task->status === 'zavrsen' ? 'text-green-600' : 'text-gray-500' }}">
                            {{ ['novi'=>'Novi','dodeljen'=>'Dodeljen','u_toku'=>'U toku','zavrsen'=>'Zavrsen'][$task->status] ?? $task->status }}
                        </span>
                        @endif
                    </td>
                    <td class="td text-right">
                        @if($caseDept->isActive())
                        <form method="POST" action="{{ route('jna.tasks.destroy', [$case, $task]) }}"
                              onsubmit="return confirm('Obrisati zadatak?')" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-400 hover:text-red-600 text-xs">✕</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="td text-center text-gray-400 py-8">Nema JNA zadataka za ovaj slucaj.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($caseDept->isActive())
    {{-- Novi zadatak --}}
    <div class="card max-w-2xl">
        <div class="card-header"><h3 class="text-sm font-semibold text-gray-700">Novi JNA zadatak</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('jna.tasks.store', $case) }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="form-label">Naziv zadatka *</label>
                        <input name="title" type="text" required class="form-input" placeholder="npr. Prevoz pokojnika">
                    </div>
                    <div>
                        <label class="form-label">Polazna lokacija</label>
                        <input name="departure_location" type="text" class="form-input" placeholder="npr. KC Beograd">
                    </div>
                    <div>
                        <label class="form-label">Odrediste</label>
                        <input name="destination" type="text" class="form-input" placeholder="npr. Novo groblje">
                    </div>
                    <div>
                        <label class="form-label">Radnik</label>
                        <select name="assigned_worker_id" class="form-select">
                            <option value="">— Bez radnika —</option>
                            @foreach($workers as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Vozilo</label>
                        <select name="vehicle_id" class="form-select">
                            <option value="">— Bez vozila —</option>
                            @foreach($vehicles as $v)
                            <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->registration_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Rok</label>
                        <input name="due_at" type="datetime-local" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Napomena</label>
                        <input name="note" type="text" class="form-input" placeholder="Opciono...">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">Dodaj zadatak</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</x-app-layout>

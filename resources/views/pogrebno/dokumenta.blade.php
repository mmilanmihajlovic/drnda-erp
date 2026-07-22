<x-app-layout>
    <x-slot name="title">Dokumenta — {{ $case->case_number }}</x-slot>
    <x-slot name="subtitle">{{ $case->deceased_name }}</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('pogrebno.show', $case) }}" class="btn-secondary text-sm">← Nazad</a>
    </x-slot>

    <div class="max-w-3xl">

        {{-- Upload --}}
        <div class="card mb-6">
            <div class="card-header"><h2 class="text-sm font-semibold text-gray-700">Postavi dokument</h2></div>
            <div class="card-body">
                <form method="POST" action="{{ route('pogrebno.dokumenta.store', $case) }}"
                      enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Fajl *</label>
                            <input name="file" type="file" required
                                   class="block w-full text-sm text-gray-700 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-sm file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                            @error('file')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label">Naziv dokumenta</label>
                            <input name="name" type="text" class="form-input" placeholder="Ostavite prazno za originalni naziv">
                        </div>
                        <div>
                            <label class="form-label">Tip dokumenta *</label>
                            <select name="type" required class="form-select">
                                <option value="">— Izaberi tip —</option>
                                @foreach(['Smrtovnica','Saglasnost','Faktura','Ugovor','Dozvola','Ostalo'] as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                                @endforeach
                            </select>
                            @error('type')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary">Postavi dokument</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Lista --}}
        <div class="card">
            <table class="table-base">
                <thead>
                    <tr>
                        <th class="th">Naziv</th>
                        <th class="th">Tip</th>
                        <th class="th">Velicina</th>
                        <th class="th">Postavio</th>
                        <th class="th">Datum</th>
                        <th class="th w-20"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($documents as $doc)
                    <tr class="tr-hover">
                        <td class="td">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-sm font-medium truncate max-w-48">{{ $doc->name }}</span>
                            </div>
                        </td>
                        <td class="td text-gray-600 text-sm">{{ $doc->type }}</td>
                        <td class="td text-gray-500 text-sm">{{ $doc->formatted_size }}</td>
                        <td class="td text-gray-500 text-sm">{{ optional($doc->uploadedBy)->name ?? '—' }}</td>
                        <td class="td text-gray-500 text-sm">{{ $doc->created_at->format('d.m.Y') }}</td>
                        <td class="td text-right">
                            <a href="{{ route('pogrebno.dokumenta.download', [$case, $doc]) }}"
                               class="text-yellow-600 hover:text-yellow-700 text-sm font-medium mr-2">↓</a>
                            <form method="POST" action="{{ route('pogrebno.dokumenta.destroy', [$case, $doc]) }}"
                                  class="inline" onsubmit="return confirm('Obrisati dokument?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 text-sm">✕</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="td text-center text-gray-400 py-8">Nema dokumenata.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

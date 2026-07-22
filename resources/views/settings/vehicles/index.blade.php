<x-app-layout>
    <x-slot name="title">Vozila</x-slot>
    <x-slot name="headerActions"><a href="{{ route('settings.vehicles.create') }}" class="btn-primary"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Dodaj vozilo</a></x-slot>
    <div class="card"><table class="table-base">
        <thead><tr><th class="th">Naziv</th><th class="th">Registracija</th><th class="th">Tip</th><th class="th">Status</th><th class="th w-24"></th></tr></thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @forelse($vehicles as $v)
            <tr class="tr-hover">
                <td class="td font-medium">{{ $v->name }}</td><td class="td font-mono text-sm">{{ $v->registration_number }}</td>
                <td class="td text-gray-600">{{ $v->type }}</td>
                <td class="td">@if($v->active)<span class="status-done">Aktivno</span>@else<span class="status-error">Neaktivno</span>@endif</td>
                <td class="td text-right">
                    <a href="{{ route('settings.vehicles.edit', $v) }}" class="text-sm text-yellow-600 hover:text-yellow-700 font-medium mr-3">Izmeni</a>
                    <form method="POST" action="{{ route('settings.vehicles.destroy', $v) }}" class="inline" onsubmit="return confirm('Obrisati vozilo?')">@csrf @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-700">Obrisi</button>
                    </form>
                </td>
            </tr>
            @empty<tr><td colspan="5" class="td text-center text-gray-400 py-8">Nema vozila.</td></tr>@endforelse
        </tbody>
    </table></div>
</x-app-layout>

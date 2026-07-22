<x-app-layout>
    <x-slot name="title">Korisnici</x-slot>
    <x-slot name="headerActions"><a href="{{ route('settings.users.create') }}" class="btn-primary"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Novi korisnik</a></x-slot>
    <div class="card"><table class="table-base">
        <thead><tr><th class="th">Ime</th><th class="th">Email</th><th class="th">Uloga</th><th class="th">Status</th><th class="th w-24"></th></tr></thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @forelse($users as $user)
            <tr class="tr-hover">
                <td class="td font-medium">{{ $user->name }}</td>
                <td class="td text-gray-600">{{ $user->email }}</td>
                <td class="td">@foreach($user->roles as $r)<span class="status-chip bg-yellow-100 text-yellow-800 mr-1">{{ $r->name }}</span>@endforeach</td>
                <td class="td">@if($user->active)<span class="status-done">Aktivan</span>@else<span class="status-error">Neaktivan</span>@endif</td>
                <td class="td text-right">
                    <a href="{{ route('settings.users.edit', $user) }}" class="text-sm text-yellow-600 hover:text-yellow-700 font-medium mr-3">Izmeni</a>
                    <form method="POST" action="{{ route('settings.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Obrisati {{ $user->name }}?')">@csrf @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-700">Obrisi</button>
                    </form>
                </td>
            </tr>
            @empty<tr><td colspan="5" class="td text-center text-gray-400 py-8">Nema korisnika.</td></tr>@endforelse
        </tbody>
    </table>@if($users->hasPages())<div class="px-4 py-3 border-t border-gray-100">{{ $users->links() }}</div>@endif</div>
</x-app-layout>

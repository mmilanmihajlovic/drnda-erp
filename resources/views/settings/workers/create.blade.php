<x-app-layout>
    <x-slot name="title">Novi radnik</x-slot>
    <div class="max-w-2xl"><div class="card"><div class="card-body"><form method="POST" action="{{ route('settings.workers.store') }}" class="space-y-5">@csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div><label class="form-label">Ime i prezime *</label><input name="name" type="text" value="{{ old('name') }}" required class="form-input">@error('name')<p class="form-error">{{ $message }}</p>@enderror</div>
            <div><label class="form-label">Odeljenje *</label><select name="department_id" required class="form-select"><option value="">— Izaberi —</option>@foreach($departments as $d)<option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>@endforeach</select>@error('department_id')<p class="form-error">{{ $message }}</p>@enderror</div>
            <div><label class="form-label">Korisnicki nalog</label><select name="user_id" class="form-select"><option value="">— Bez naloga —</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>@endforeach</select></div>
            <div class="flex items-end pb-1"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="active" value="1" checked class="rounded border-gray-300 text-yellow-600"><span class="text-sm font-medium text-gray-700">Aktivan</span></label></div>
        </div>
        <div class="flex gap-3"><button type="submit" class="btn-primary">Sacuvaj</button><a href="{{ route('settings.workers.index') }}" class="btn-secondary">Odustani</a></div>
    </form></div></div></div>
</x-app-layout>

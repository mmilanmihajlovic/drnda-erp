<x-app-layout>
    <x-slot name="title">Novi artikal/usluga</x-slot>
    <div class="max-w-2xl"><div class="card"><div class="card-body"><form method="POST" action="{{ route('settings.items.store') }}" class="space-y-5">@csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2"><label class="form-label">Naziv *</label><input name="name" type="text" value="{{ old('name') }}" required class="form-input">@error('name')<p class="form-error">{{ $message }}</p>@enderror</div>
            <div><label class="form-label">Odeljenje *</label><select name="department_id" required class="form-select"><option value="">— Izaberi —</option>@foreach($departments as $d)<option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>@endforeach</select>@error('department_id')<p class="form-error">{{ $message }}</p>@enderror</div>
            <div><label class="form-label">Tip *</label><select name="type" required class="form-select">@foreach(['Usluga','Artikal','Paket'] as $t)<option value="{{ $t }}" {{ old('type') == $t ? 'selected' : '' }}>{{ $t }}</option>@endforeach</select></div>
            <div><label class="form-label">Cena (RSD) *</label><input name="default_price" type="number" step="0.01" min="0" value="{{ old('default_price', '0.00') }}" required class="form-input">@error('default_price')<p class="form-error">{{ $message }}</p>@enderror</div>
            <div class="flex items-end pb-1"><label class="flex items-center gap-2"><input type="checkbox" name="active" value="1" checked class="rounded border-gray-300 text-yellow-600"><span class="text-sm font-medium text-gray-700">Aktivan</span></label></div>
        </div>
        <div class="flex gap-3"><button type="submit" class="btn-primary">Sacuvaj</button><a href="{{ route('settings.items.index') }}" class="btn-secondary">Odustani</a></div>
    </form></div></div></div>
</x-app-layout>

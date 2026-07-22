<x-app-layout>
    <x-slot name="title">Izmena slucaja — {{ $case->case_number }}</x-slot>

    <div class="max-w-2xl" x-data="{ caseType: '{{ $case->case_type }}' }">
        <div class="card">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-gray-700">Izmena podataka slucaja</h2>
                <span class="font-mono text-sm text-gray-500">{{ $case->case_number }}</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('cases.update', $case) }}" class="space-y-6">
                    @csrf @method('PUT')

                    <div>
                        <label class="form-label">Tip slucaja *</label>
                        <div class="flex gap-3 mt-1">
                            <label class="flex items-center gap-2 cursor-pointer px-4 py-2.5 rounded-lg border-2 transition-colors"
                                   :class="caseType === 'domaci' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200'">
                                <input type="radio" name="case_type" value="domaci" x-model="caseType">
                                <span class="text-sm font-medium">Domaci</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer px-4 py-2.5 rounded-lg border-2 transition-colors"
                                   :class="caseType === 'ino' ? 'border-purple-500 bg-purple-50' : 'border-gray-200'">
                                <input type="radio" name="case_type" value="ino" x-model="caseType">
                                <span class="text-sm font-medium">Ino</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="form-label">Ime i prezime pokojnika *</label>
                            <input name="deceased_name" type="text"
                                   value="{{ old('deceased_name', $case->deceased_name) }}"
                                   required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Kontakt — ime</label>
                            <input name="family_contact_name" type="text"
                                   value="{{ old('family_contact_name', $case->family_contact_name) }}"
                                   class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Kontakt — telefon</label>
                            <input name="family_contact_phone" type="text"
                                   value="{{ old('family_contact_phone', $case->family_contact_phone) }}"
                                   class="form-input">
                        </div>
                    </div>

                    <div x-show="caseType === 'domaci'" x-transition>
                        <label class="form-label">Mesto sahrane</label>
                        <input name="location" type="text"
                               value="{{ old('location', $case->location) }}"
                               class="form-input">
                    </div>

                    <div x-show="caseType === 'ino'" x-transition>
                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label class="form-label">Polaziste</label>
                                <input name="route_from" type="text"
                                       value="{{ old('route_from', $case->route_from) }}"
                                       class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Odrediste</label>
                                <input name="route_to" type="text"
                                       value="{{ old('route_to', $case->route_to) }}"
                                       class="form-input">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Datum i vreme sahrane</label>
                        <input name="funeral_at" type="datetime-local"
                               value="{{ old('funeral_at', $case->funeral_at?->format('Y-m-d\TH:i')) }}"
                               class="form-input">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary">Sacuvaj izmene</button>
                        <a href="{{ route('cases.show', $case) }}" class="btn-secondary">Odustani</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

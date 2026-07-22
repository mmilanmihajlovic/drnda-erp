<x-layouts.guest>
<div class="bg-gray-900 rounded-2xl shadow-2xl border border-gray-800 p-8">
    <div class="text-center mb-6">
        <h1 class="text-xl font-bold text-white">Resetuj lozinku</h1>
        <p class="text-gray-400 text-sm mt-1">Unesite e-mail — poslaćemo vam link za reset.</p>
    </div>
    @if (session('status'))
        <div class="mb-4 text-sm text-green-400">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm text-gray-300 mb-1">E-mail adresa</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5">
            @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="w-full py-3 rounded-lg font-semibold text-sm text-gray-950" style="background:#c9a84c;">
            Pošalji link za reset
        </button>
    </form>
    <p class="text-center text-sm text-gray-400 mt-4">
        <a href="{{ route('login') }}" class="text-yellow-600">Nazad na prijavu</a>
    </p>
</div>
</x-layouts.guest>

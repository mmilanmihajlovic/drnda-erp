<x-layouts.guest>
<div class="bg-gray-900 rounded-2xl shadow-2xl border border-gray-800 p-8">
    <div class="text-center mb-6">
        <h1 class="text-xl font-bold text-white">Registracija</h1>
    </div>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm text-gray-300 mb-1">Ime</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5">
            @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm text-gray-300 mb-1">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5">
            @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm text-gray-300 mb-1">Lozinka</label>
            <input type="password" name="password" required
                class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5">
            @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-6">
            <label class="block text-sm text-gray-300 mb-1">Potvrdi lozinku</label>
            <input type="password" name="password_confirmation" required
                class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5">
        </div>
        <button type="submit" class="w-full py-3 rounded-lg font-semibold text-sm text-gray-950" style="background:#c9a84c;">
            Registruj se
        </button>
    </form>
    <p class="text-center text-sm text-gray-400 mt-4">
        <a href="{{ route('login') }}" class="text-yellow-600">Prijava</a>
    </p>
</div>
</x-layouts.guest>

<x-layouts.guest>
<div class="bg-gray-900 rounded-2xl shadow-2xl border border-gray-800 p-8">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gold-DEFAULT" style="color:#c9a84c;">DRNDA ERP</h1>
        <p class="text-gray-400 text-sm mt-1">Prijavite se na sistem</p>
    </div>

    @if (session('status'))
        <div class="mb-4 text-sm text-green-400">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm text-gray-300 mb-1">E-mail adresa</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-yellow-600">
            @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-6">
            <label class="block text-sm text-gray-300 mb-1">Lozinka</label>
            <input type="password" name="password" required
                class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:border-yellow-600">
            @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-center justify-between mb-6">
            <label class="flex items-center gap-2 text-sm text-gray-400">
                <input type="checkbox" name="remember" class="rounded"> Zapamti me
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-yellow-600 hover:text-yellow-500">Zaboravili ste lozinku?</a>
            @endif
        </div>
        <button type="submit" class="w-full py-3 rounded-lg font-semibold text-sm text-gray-950" style="background:#c9a84c;">
            Prijavite se
        </button>
    </form>
</div>
</x-layouts.guest>

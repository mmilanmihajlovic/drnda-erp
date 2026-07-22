<!DOCTYPE html>
<html lang="sr" class="h-full">
<head><meta charset="utf-8"><title>Verifikacija — DRNDA ERP</title>@vite(['resources/css/app.css'])</head>
<body class="h-full bg-[#1a1f2e] flex items-center justify-center">
<div class="max-w-sm mx-4 bg-[#232840] rounded-2xl border border-[#2a3045] p-8">
    <h2 class="text-lg font-bold text-white mb-3">Verifikacija email adrese</h2>
    <p class="text-sm text-[#8a94b0] mb-5">Molimo vas da verifikujete vasu email adresu klikom na link koji smo poslali.</p>
    @if (session('status') == 'verification-link-sent')
    <div class="mb-4 text-sm text-green-400">Novi verifikacioni link je poslat.</div>
    @endif
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="w-full py-3 rounded-lg bg-yellow-600 text-white text-sm font-semibold hover:bg-yellow-700 transition-colors">Posalji ponovo</button>
    </form>
    <form method="POST" action="{{ route('logout') }}" class="mt-3">@csrf
        <button class="w-full py-2.5 text-sm text-[#8a94b0] hover:text-white">Odjavi se</button>
    </form>
</div>
</body>
</html>

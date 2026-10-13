<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Призрачное Бюро')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
<nav class="bg-slate-900 border-b border-slate-800">
    <div class="max-w-6xl mx-auto px-6 py-3 flex items-center gap-6">
        <a href="{{ route('ghosts.index') }}" class="font-bold text-lg">👻 Призрачное Бюро</a>
        <a href="{{ route('ghosts.index') }}"
           class="text-sm hover:text-sky-400 {{ request()->routeIs('ghosts.*') ? 'text-sky-400' : 'text-slate-300' }}">Заявки</a>
        <a href="{{ route('locations.index') }}"
           class="text-sm hover:text-sky-400 {{ request()->routeIs('locations.*') ? 'text-sky-400' : 'text-slate-300' }}">Места</a>
        <a href="{{ route('report.index') }}"
           class="text-sm hover:text-sky-400 {{ request()->routeIs('report.*') ? 'text-sky-400' : 'text-slate-300' }}">Отчёт</a>
        <a href="{{ route('test-scenarios') }}" class="ml-auto text-sm text-slate-400 hover:text-sky-400">Тесты</a>
        <a href="{{ route('ai-worklog') }}" class="ml-auto text-sm text-slate-400 hover:text-sky-400">AI Worklog</a>
    </div>
</nav>
<main class="max-w-6xl mx-auto px-6 py-8">
    @if (session('success'))
        <div
            class="mb-4 bg-emerald-900/50 border border-emerald-600 text-emerald-200 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if (session('warning'))
        <div
            class="mb-4 bg-amber-900/50 border border-amber-600 text-amber-200 px-4 py-3 rounded">{{ session('warning') }}</div>
    @endif
    @if (session('error'))
        <div
            class="mb-4 bg-red-900/50 border border-red-600 text-red-200 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif
    @yield('content')
</main>
</body>
</html>

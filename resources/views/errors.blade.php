<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Призрачное бюро — просыпается</title>
    <meta http-equiv="refresh" content="15">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center">
<div class="max-w-lg mx-auto text-center px-6">
    <div class="text-7xl mb-6 animate-pulse">👻</div>
    <h1 class="text-2xl font-bold mb-3">Призрачное бюро просыпается</h1>
    <p class="text-slate-400 mb-6">
        Первый запуск после паузы занимает до 2 минут. Не закрывайте вкладку —
        страница обновится автоматически.
    </p>
    <div class="bg-slate-800 rounded-lg p-4 text-sm text-slate-300">
        <div class="mb-2">Что происходит прямо сейчас:</div>
        <ul class="space-y-1 text-left">
            <li>• контейнер поднимается из спящего режима</li>
            <li>• применяются миграции</li>
            <li>• прогревается кеш конфигов</li>
        </ul>
    </div>
    <button onclick="location.reload()"
            class="mt-6 bg-sky-600 hover:bg-sky-500 px-5 py-2 rounded-lg text-sm">
        Обновить сейчас
    </button>
</div>
</body>
</html>

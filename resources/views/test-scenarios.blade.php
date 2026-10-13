<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Сценарии проверки</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen">
<div class="max-w-4xl mx-auto px-6 py-12">

    <header class="mb-10">
        <h1 class="text-4xl font-bold mb-2">Сценарии проверки</h1>
        <p class="text-slate-400">Ручные и автоматические тесты приложения</p>
        <a href="{{ url('/') }}" class="text-sky-400 hover:underline text-sm">&larr; на главную</a>
    </header>

    @php
        $scenarios = [
            [
                'num' => 1,
                'title' => 'Пустой список заявок',
                'setup' => 'Чистая база. Выполнить <code>php artisan migrate:fresh</code> (без сидера).',
                'steps' => [
                    'Открыть <code>/ghosts</code>.',
                    'Проверить счётчики: Всего 0, Расселено 0, Без места 0, Просрочено 0.',
                    'Проверить центральный блок.',
                ],
                'expected' => 'Показана заглушка с иконкой призрака, текстом «Пока нет ни одной заявки» и кнопкой «+ Создать заявку». Кнопка ведёт на форму создания. Никаких ошибок в консоли и в логах.',
            ],
            [
                'num' => 2,
                'title' => 'Заявка без подходящего места',
                'setup' => 'Выполнить <code>php artisan migrate:fresh --seed</code>. Дождаться, пока «Тень Чердаков» займёт «Чердак поместья» (capacity=1).',
                'steps' => [
                    'Открыть заявку «Хранитель Чердаков» (needs_attic + fears_mirrors + no_humans_nearby).',
                    'Нажать «⚡ Подобрать автоматически».',
                    'Прокрутить вниз к «Анализ по всем местам».',
                ],
                'expected' => 'Флеш «Подходящее место не найдено — смотрите причины ниже». Место не назначено. В анализе у «Замка Тьмы» — блокер «Боится зеркал», у «Маяка» — «Нужен чердак», у «Чердака поместья» — «Место переполнено (1/1)».',
            ],
            [
                'num' => 3,
                'title' => 'Место переполнено',
                'setup' => 'Сидовая база. «Чердак поместья» capacity=1, занят «Тенью Чердаков».',
                'steps' => [
                    'Открыть заявку «Сэр Призрак».',
                    'В анализе найти «Чердак поместья».',
                    'Нажать «Назначить».',
                ],
                'expected' => 'Флеш красного цвета: «ВНИМАНИЕ! Выбор конфликтует с условиями: Место переполнено (1/1)». Назначение всё равно сохраняется (ручное переопределение разрешено), но оператор видит проблему.',
            ],
            [
                'num' => 4,
                'title' => 'Ручной выбор конфликтует с особым условием',
                'setup' => 'Сидовая база. У «Зеркального Эха» есть условие fears_mirrors.',
                'steps' => [
                    'Открыть заявку «Зеркальное Эхо».',
                    'В анализе найти «Замок Тьмы» (has_mirrors = true).',
                    'Нажать «Назначить».',
                ],
                'expected' => 'Флеш красного цвета с причиной «Боится зеркал — в месте есть зеркала». В списке заявок у этой записи появится место «Замок Тьмы» с оценкой ниже 100.',
            ],
            [
                'num' => 5,
                'title' => 'Успешное автораспределение и корректный отчёт',
                'setup' => 'Выполнить <code>php artisan migrate:fresh --seed</code>.',
                'steps' => [
                    'На странице <code>/ghosts</code> нажать «⚡ Автораспределение».',
                    'Дождаться флеш-сообщения.',
                    'Открыть <code>/report</code>.',
                    'Сверить цифры: Всего 7, Расселено + Без места = 7.',
                    'Проверить блок «Проблемные заявки».',
                    'Проверить блок «Перегруженные места».',
                ],
                'expected' => 'Флеш «Автораспределение: расселено X, без места Y». В отчёте X + Y = 7. «Дядюшка Сквозняк» отмечен как «просрочен». «Хранитель Чердаков» — в проблемных. «Чердак поместья» — в перегруженных (1/1).',
            ],
        ];
    @endphp

    @foreach ($scenarios as $s)
        <div class="mb-6 bg-slate-800 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-sky-400 mb-3">
                Сценарий {{ $s['num'] }}. {{ $s['title'] }}
            </h2>

            <div class="mb-3">
                <div class="text-xs uppercase text-slate-500 mb-1">Подготовка</div>
                <div class="text-sm text-slate-300">{!! $s['setup'] !!}</div>
            </div>

            <div class="mb-3">
                <div class="text-xs uppercase text-slate-500 mb-1">Шаги</div>
                <ol class="list-decimal list-inside text-sm space-y-1">
                    @foreach ($s['steps'] as $step)
                        <li>{!! $step !!}</li>
                    @endforeach
                </ol>
            </div>

            <div>
                <div class="text-xs uppercase text-slate-500 mb-1">Ожидаемый результат</div>
                <div class="text-sm text-emerald-300">{!! $s['expected'] !!}</div>
            </div>
        </div>
    @endforeach

    <section class="mt-10 bg-slate-800 rounded-xl p-6">
        <h2 class="text-lg font-semibold text-sky-400 mb-3">Автоматические тесты (PHPUnit)</h2>
        <p class="text-sm text-slate-300 mb-3">
            Лежат в <code>tests/Feature/</code> и <code>tests/Unit/</code>. Запуск:
            <code class="bg-slate-900 px-2 py-1 rounded">docker compose exec app php artisan test</code>
        </p>
        <ul class="text-sm space-y-1 text-slate-300">
            <li>• <b>GhostMatcherTest</b> — score считается корректно, блокеры срабатывают, температура vs влажность влияет.</li>
            <li>• <b>GhostControllerTest</b> — пустой список, автораспределение, ручное назначение с конфликтом, снятие назначения.</li>
            <li>• <b>LocationTest</b> — метод <code>free()</code>, <code>isFull()</code>, <code>occupied()</code>.</li>
            <li>• <b>RuleProviderTest</b> — все 6 условий находятся по ключу, options возвращает 6 меток.</li>
            <li>• <b>ConditionResultTest</b> — статические фабрики возвращают правильный type.</li>
        </ul>
    </section>

    <footer class="text-center text-xs text-slate-500 mt-12">
        Сценарии проверки — {{ now()->format('d.m.Y') }}
    </footer>

</div>
</body>
</html>

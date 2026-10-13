@extends('layouts.app')
@section('title', $ghost->name)
@section('content')
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('ghosts.index') }}" class="text-slate-400 hover:text-slate-200">← Назад</a>
        <h1 class="text-2xl font-bold">{{ $ghost->name }}</h1>
    </div>

    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-1 space-y-4">
            <div class="bg-slate-900 border border-slate-800 rounded p-5">
                <h2 class="text-sm uppercase text-slate-500 mb-3">Заявка</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Тревожность</dt>
                        <dd>{{ $ghost->anxiety_level }}/10</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Температура</dt>
                        <dd>{{ ['cold'=>'❄ холод','neutral'=>'〜 нейтр','warm'=>'🔥 тепло'][$ghost->favorite_temperature] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Дедлайн</dt>
                        <dd class="{{ $ghost->isExpired() ? 'text-red-400' : '' }}">{{ $ghost->deadline->format('d.m.Y') }}</dd>
                    </div>
                </dl>
                @if ($ghost->special_conditions)
                    <div class="mt-4 pt-4 border-t border-slate-800">
                        <div class="text-xs text-slate-500 mb-2">Особые условия:</div>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($ghost->special_conditions as $c)
                                <span
                                    class="text-xs bg-slate-800 px-2 py-1 rounded">{{ $ghost->conditionLabel($c) }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            @if ($ghost->location)
                <div class="bg-emerald-900/30 border border-emerald-700 rounded p-5">
                    <h2 class="text-sm uppercase text-emerald-400 mb-2">Текущее место</h2>
                    <div class="text-lg font-semibold">{{ $ghost->location->name }}</div>
                    <div class="text-xs text-slate-400 mt-1">Оценка: {{ $ghost->match_score }}</div>
                    @if ($ghost->match_reason)
                        <div class="text-xs text-slate-300 mt-2">{{ $ghost->match_reason }}</div>
                    @endif
                    <form method="POST" action="{{ route('ghosts.unassign', $ghost) }}" class="mt-3">
                        @csrf
                        <button class="text-xs text-red-400 hover:underline">Снять назначение</button>
                    </form>
                </div>
            @else
                <div class="bg-amber-900/30 border border-amber-700 rounded p-5">
                    <div class="text-sm text-amber-300">Место не назначено</div>
                </div>
            @endif

            <form method="POST" action="{{ route('ghosts.auto', $ghost) }}">
                @csrf
                <button class="w-full bg-sky-600 hover:bg-sky-500 px-4 py-2 rounded text-sm">⚡ Подобрать автоматически
                </button>
            </form>
        </div>

        <div class="col-span-2 space-y-6">
            @if ($best)
                <div class="bg-emerald-900/20 border border-emerald-800 rounded p-6">
                    <div class="text-xs uppercase text-emerald-400 mb-2">Рекомендация</div>
                    <div class="text-xl font-bold mb-2">{{ $best['location']->name }}</div>
                    <div class="text-sm text-emerald-300 mb-3">Оценка: {{ $best['result']['score'] }} / 150</div>
                    <ul class="text-sm space-y-1">
                        @foreach ($best['result']['reasons'] as $r)
                            <li class="text-slate-300">+ {{ $r }}</li>
                        @endforeach
                        @foreach ($best['result']['warnings'] as $w)
                            <li class="text-amber-300">⚠ {{ $w }}</li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="bg-red-900/20 border border-red-800 rounded p-6">
                    <div class="text-xs uppercase text-red-400 mb-2">Невозможно переселить</div>
                    <div class="text-lg font-semibold mb-2">Ни одно место не подходит</div>
                    <div class="text-sm text-slate-300">Причины по каждому месту — ниже.</div>
                </div>
            @endif

            <div>
                <h2 class="text-lg font-bold mb-3">Анализ по всем местам</h2>
                <div class="space-y-3">
                    @foreach ($candidates as $c)
                        @php
                            $r = $c['result']; $loc = $c['location'];
                            $color = !$r['possible'] ? 'red' : ($r['score'] >= 100 ? 'emerald' : ($r['score'] >= 70 ? 'sky' : ($r['score'] >= 40 ? 'amber' : 'red')));
                        @endphp
                        <div class="bg-slate-900 border border-slate-800 rounded p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="font-semibold">{{ $loc->name }}</span>
                                        <span class="text-xs text-slate-500">{{ $loc->type }}</span>
                                        @if (!$r['possible'])
                                            <span
                                                class="text-xs bg-red-900 text-red-200 px-2 py-0.5 rounded">невозможно</span>
                                        @else
                                            <span
                                                class="text-xs bg-{{ $color }}-900 text-{{ $color }}-200 px-2 py-0.5 rounded">оценка {{ $r['score'] }}</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-slate-500 mb-2">
                                        🕯 {{ ['dark'=>'темно','dim'=>'полумрак','bright'=>'светло'][$loc->lighting] }} ·
                                        🔊 {{ ['quiet'=>'тихо','moderate'=>'умеренно','loud'=>'шумно'][$loc->noise_level] }}
                                        ·
                                        💧 {{ ['dry'=>'сухо','normal'=>'норм','damp'=>'сыро'][$loc->humidity] }} ·
                                        👥 {{ $loc->has_people ? 'есть люди' : 'без людей' }} ·
                                        📦 {{ $loc->ghosts()->count() }}/{{ $loc->capacity }}
                                    </div>
                                    @foreach (['reasons'=>'text-emerald-300|+ ','warnings'=>'text-amber-300|⚠ ','blockers'=>'text-red-300|✕ '] as $key => $meta)
                                        @php [$cls, $prefix] = explode('|', $meta); @endphp
                                        @foreach ($r[$key] as $line)
                                            <div class="text-xs {{ $cls }}">{{ $prefix }}{{ $line }}</div>
                                        @endforeach
                                    @endforeach
                                </div>
                                <form method="POST" action="{{ route('ghosts.assign', $ghost) }}">
                                    @csrf
                                    <input type="hidden" name="location_id" value="{{ $loc->id }}">
                                    <button
                                        class="text-xs bg-slate-800 hover:bg-slate-700 px-3 py-1.5 rounded whitespace-nowrap">
                                        Назначить
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

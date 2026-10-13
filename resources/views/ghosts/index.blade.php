@extends('layouts.app')
@section('title', 'Заявки')
@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Заявки от привидений</h1>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('ghosts.auto-all') }}">
                @csrf
                <button class="bg-sky-600 hover:bg-sky-500 px-4 py-2 rounded text-sm">⚡ Автораспределение</button>
            </form>
            <a href="{{ route('ghosts.create') }}"
               class="bg-emerald-600 hover:bg-emerald-500 px-4 py-2 rounded text-sm">+ Новая заявка</a>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-slate-900 border border-slate-800 rounded p-4">
            <div class="text-xs text-slate-400">Всего</div>
            <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded p-4">
            <div class="text-xs text-slate-400">Расселено</div>
            <div class="text-2xl font-bold text-emerald-400">{{ $stats['assigned'] }}</div>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded p-4">
            <div class="text-xs text-slate-400">Без места</div>
            <div class="text-2xl font-bold text-red-400">{{ $stats['unassigned'] }}</div>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded p-4">
            <div class="text-xs text-slate-400">Просрочено</div>
            <div class="text-2xl font-bold text-amber-400">{{ $stats['expired'] }}</div>
        </div>
    </div>

    @if ($ghosts->isEmpty())
        <div class="bg-slate-900 border border-slate-800 rounded p-12 text-center">
            <div class="text-6xl mb-4">👻</div>
            <div class="text-lg text-slate-300 mb-2">Пока нет ни одной заявки</div>
            <div class="text-sm text-slate-500 mb-4">Добавьте первую заявку, чтобы начать подбор.</div>
            <a href="{{ route('ghosts.create') }}"
               class="inline-block bg-sky-600 hover:bg-sky-500 px-4 py-2 rounded text-sm">+ Создать заявку</a>
        </div>
    @else
        <div class="bg-slate-900 border border-slate-800 rounded overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-800/50 text-slate-400 text-xs uppercase">
                <tr>
                    <th class="text-left px-4 py-3">Имя</th>
                    <th class="text-left px-4 py-3">Тревожность</th>
                    <th class="text-left px-4 py-3">Темп.</th>
                    <th class="text-left px-4 py-3">Дедлайн</th>
                    <th class="text-left px-4 py-3">Место</th>
                    <th class="text-left px-4 py-3">Оценка</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($ghosts as $ghost)
                    @php
                        $score = $ghost->match_score;
                        $cls = $score === null ? 'text-slate-600' : ($score >= 100 ? 'text-emerald-400' : ($score >= 70 ? 'text-sky-400' : ($score >= 40 ? 'text-amber-400' : 'text-red-400')));
                    @endphp
                    <tr class="border-t border-slate-800 hover:bg-slate-800/30">
                        <td class="px-4 py-3"><a href="{{ route('ghosts.show', $ghost) }}"
                                                 class="text-sky-400 hover:underline font-medium">{{ $ghost->name }}</a>
                        </td>
                        <td class="px-4 py-3">{{ $ghost->anxiety_level }}/10</td>
                        <td class="px-4 py-3 text-slate-300">{{ ['cold'=>'❄ холод','neutral'=>'〜 нейтр','warm'=>'🔥 тепло'][$ghost->favorite_temperature] }}</td>
                        <td class="px-4 py-3 {{ $ghost->isExpired() ? 'text-red-400 font-semibold' : 'text-slate-300' }}">
                            {{ $ghost->deadline->format('d.m.Y') }}
                            @if ($ghost->isExpired())
                                <span class="text-xs">(просрочен)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($ghost->location)
                                <span class="text-emerald-300">{{ $ghost->location->name }}</span>
                            @else
                                <span class="text-slate-500">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 {{ $cls }}">{{ $score ?? '—' }}</td>
                        <td class="px-4 py-3 text-right"><a href="{{ route('ghosts.show', $ghost) }}"
                                                            class="text-xs text-slate-400 hover:text-sky-400">Открыть
                                →</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection

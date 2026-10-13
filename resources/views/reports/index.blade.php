@extends('layouts.app')
@section('title', 'Отчёт')
@section('content')
    <h1 class="text-2xl font-bold mb-6">Итоговый отчёт</h1>

    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-slate-900 border border-slate-800 rounded p-5">
            <div class="text-xs text-slate-400 mb-1">Всего заявок</div>
            <div class="text-3xl font-bold">{{ $total }}</div>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded p-5">
            <div class="text-xs text-slate-400 mb-1">Расселено</div>
            <div class="text-3xl font-bold text-emerald-400">{{ $assigned }}</div>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded p-5">
            <div class="text-xs text-slate-400 mb-1">Без места</div>
            <div class="text-3xl font-bold text-red-400">{{ $unassigned }}</div>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded p-5">
            <div class="text-xs text-slate-400 mb-1">Просрочено</div>
            <div class="text-3xl font-bold text-amber-400">{{ $expired }}</div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-8">
        <div>
            <h2 class="text-lg font-bold mb-3">Проблемные заявки</h2>
            @if ($problemGhosts->isEmpty())
                <div class="bg-slate-900 border border-slate-800 rounded p-6 text-slate-500 text-sm">Проблемных заявок
                    нет.
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($problemGhosts as $g)
                        <div class="bg-slate-900 border border-slate-800 rounded p-4 flex items-center justify-between">
                            <div>
                                <a href="{{ route('ghosts.show', $g) }}"
                                   class="font-medium text-sky-400 hover:underline">{{ $g->name }}</a>
                                <div class="text-xs text-slate-500 mt-1">
                                    @if ($g->location_id === null)
                                        Не расселено
                                    @else
                                        В «{{ $g->location->name }}», оценка {{ $g->match_score }}
                                    @endif
                                </div>
                            </div>
                            @if ($g->isExpired())
                                <span class="text-xs text-red-400">просрочен</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div>
            <h2 class="text-lg font-bold mb-3">Перегруженные места</h2>
            @if ($crowded->isEmpty())
                <div class="bg-slate-900 border border-slate-800 rounded p-6 text-slate-500 text-sm">Перегруженных мест
                    нет.
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($crowded as $loc)
                        <div class="bg-red-900/20 border border-red-800 rounded p-4">
                            <div class="font-medium">{{ $loc->name }}</div>
                            <div class="text-xs text-slate-400 mt-1">Занято {{ $loc->ghosts_count }}
                                из {{ $loc->capacity }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div>
        <h2 class="text-lg font-bold mb-3">Заполненность мест</h2>
        <div class="bg-slate-900 border border-slate-800 rounded overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-800/50 text-slate-400 text-xs uppercase">
                <tr>
                    <th class="text-left px-4 py-3">Место</th>
                    <th class="text-left px-4 py-3">Тип</th>
                    <th class="text-left px-4 py-3">Занято</th>
                    <th class="text-left px-4 py-3">Свободно</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($locations as $loc)
                    @php $free = max(0, $loc->capacity - $loc->ghosts_count); @endphp
                    <tr class="border-t border-slate-800">
                        <td class="px-4 py-3">{{ $loc->name }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $loc->type }}</td>
                        <td class="px-4 py-3 {{ $loc->ghosts_count >= $loc->capacity ? 'text-red-400' : '' }}">{{ $loc->ghosts_count }}
                            / {{ $loc->capacity }}</td>
                        <td class="px-4 py-3 {{ $free > 0 ? 'text-emerald-400' : 'text-slate-500' }}">{{ $free }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@extends('layouts.app')
@section('title', 'Места')
@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Места переселения</h1>
        <a href="{{ route('locations.create') }}" class="bg-emerald-600 hover:bg-emerald-500 px-4 py-2 rounded text-sm">+
            Добавить место</a>
    </div>

    @if ($locations->isEmpty())
        <div class="bg-slate-900 border border-slate-800 rounded p-12 text-center text-slate-400">
            Мест пока нет. <a href="{{ route('locations.create') }}" class="text-sky-400 hover:underline">Добавить
                первое</a>
        </div>
    @else
        <div class="grid grid-cols-2 gap-4">
            @foreach ($locations as $loc)
                @php $full = $loc->ghosts_count >= $loc->capacity; @endphp
                <div class="bg-slate-900 border border-slate-800 rounded p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="font-semibold text-lg">{{ $loc->name }}</div>
                            <div class="text-xs text-slate-500">{{ $loc->type }}</div>
                        </div>
                        <div class="text-right">
                            <div
                                class="text-sm {{ $full ? 'text-red-400' : 'text-emerald-400' }} font-semibold">{{ $loc->ghosts_count }}
                                / {{ $loc->capacity }}</div>
                            <div class="text-xs text-slate-500">занято</div>
                        </div>
                    </div>
                    @if ($loc->description)
                        <div class="text-sm text-slate-400 mb-3">{{ $loc->description }}</div>
                    @endif
                    <div class="flex flex-wrap gap-2 text-xs">
                        <span
                            class="bg-slate-800 px-2 py-1 rounded">🕯 {{ ['dark'=>'темно','dim'=>'полумрак','bright'=>'светло'][$loc->lighting] }}</span>
                        <span
                            class="bg-slate-800 px-2 py-1 rounded">🔊 {{ ['quiet'=>'тихо','moderate'=>'умеренно','loud'=>'шумно'][$loc->noise_level] }}</span>
                        <span
                            class="bg-slate-800 px-2 py-1 rounded">💧 {{ ['dry'=>'сухо','normal'=>'норм','damp'=>'сыро'][$loc->humidity] }}</span>
                        @if ($loc->has_people)
                            <span class="bg-amber-900/50 px-2 py-1 rounded">👥 есть люди</span>
                        @endif
                        @if ($loc->has_attic)
                            <span class="bg-slate-700 px-2 py-1 rounded">чердак</span>
                        @endif
                        @if ($loc->has_mirrors)
                            <span class="bg-slate-700 px-2 py-1 rounded">зеркала</span>
                        @endif
                    </div>
                    @if ($full)
                        <div class="mt-3 text-xs text-red-400">⚠ Место переполнено</div>
                    @endif
                    <form method="POST" action="{{ route('locations.destroy', $loc) }}" class="mt-4"
                          onsubmit="return confirm('Удалить место?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-400 hover:underline">Удалить</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
@endsection

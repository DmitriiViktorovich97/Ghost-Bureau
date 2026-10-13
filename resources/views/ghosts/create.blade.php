@extends('layouts.app')
@section('title', 'Новая заявка')
@section('content')
    <h1 class="text-2xl font-bold mb-6">Новая заявка от привидения</h1>

    @if ($errors->any())
        <div class="mb-4 bg-red-900/50 border border-red-600 text-red-200 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">@foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('ghosts.store') }}"
          class="bg-slate-900 border border-slate-800 rounded p-6 space-y-4 max-w-2xl">
        @csrf
        <div>
            <label class="block text-sm text-slate-400 mb-1">Имя</label>
            <input name="name" value="{{ old('name') }}" required
                   class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm text-slate-400 mb-1">Уровень тревожности: <span
                    id="anxiety-val">5</span></label>
            <input type="range" name="anxiety_level" min="1" max="10" value="{{ old('anxiety_level', 5) }}" required
                   oninput="document.getElementById('anxiety-val').textContent = this.value" class="w-full">
        </div>
        <div>
            <label class="block text-sm text-slate-400 mb-1">Любимая температура</label>
            <select name="favorite_temperature" required
                    class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2">
                <option value="cold" @selected(old('favorite_temperature')==='cold')>❄ Холод</option>
                <option value="neutral" @selected(old('favorite_temperature')==='neutral')>〜 Нейтрально</option>
                <option value="warm" @selected(old('favorite_temperature')==='warm')>🔥 Тепло</option>
            </select>
        </div>
        <div>
            <label class="block text-sm text-slate-400 mb-1">Дедлайн переселения</label>
            <input type="date" name="deadline" value="{{ old('deadline', now()->addDays(7)->toDateString()) }}" required
                   class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm text-slate-400 mb-2">Особые условия</label>
            <div class="grid grid-cols-2 gap-2">
                @foreach ($conditions as $key => $label)
                    <label
                        class="flex items-center gap-2 bg-slate-800 border border-slate-700 rounded px-3 py-2 cursor-pointer">
                        <input type="checkbox" name="special_conditions[]"
                               value="{{ $key }}" @checked(in_array($key, old('special_conditions', [])))>
                        <span class="text-sm">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="flex gap-2 pt-2">
            <button class="bg-sky-600 hover:bg-sky-500 px-4 py-2 rounded text-sm">Сохранить</button>
            <a href="{{ route('ghosts.index') }}" class="px-4 py-2 rounded text-sm text-slate-400 hover:text-slate-200">Отмена</a>
        </div>
    </form>
@endsection

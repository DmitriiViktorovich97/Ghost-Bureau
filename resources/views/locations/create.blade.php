@extends('layouts.app')
@section('title', 'Новое место')
@section('content')
    <h1 class="text-2xl font-bold mb-6">Новое место переселения</h1>

    @if ($errors->any())
        <div class="mb-4 bg-red-900/50 border border-red-600 text-red-200 px-4 py-3 rounded text-sm">
            <ul class="list-disc list-inside">@foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('locations.store') }}"
          class="bg-slate-900 border border-slate-800 rounded p-6 space-y-4 max-w-2xl">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm text-slate-400 mb-1">Название</label><input name="name"
                                                                                         value="{{ old('name') }}"
                                                                                         required
                                                                                         class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2">
            </div>
            <div><label class="block text-sm text-slate-400 mb-1">Тип</label><input name="type"
                                                                                    value="{{ old('type') }}" required
                                                                                    placeholder="замок, маяк…"
                                                                                    class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2">
            </div>
        </div>
        <div><label class="block text-sm text-slate-400 mb-1">Вместимость</label><input type="number" name="capacity"
                                                                                        value="{{ old('capacity', 3) }}"
                                                                                        min="1" required
                                                                                        class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2">
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div><label class="block text-sm text-slate-400 mb-1">Освещение</label>
                <select name="lighting" class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2">
                    <option value="dark" @selected(old('lighting')==='dark')>Темно</option>
                    <option value="dim" @selected(old('lighting')==='dim')>Полумрак</option>
                    <option value="bright" @selected(old('lighting')==='bright')>Светло</option>
                </select></div>
            <div><label class="block text-sm text-slate-400 mb-1">Шум</label>
                <select name="noise_level" class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2">
                    <option value="quiet" @selected(old('noise_level')==='quiet')>Тихо</option>
                    <option value="moderate" @selected(old('noise_level')==='moderate')>Умеренно</option>
                    <option value="loud" @selected(old('noise_level')==='loud')>Шумно</option>
                </select></div>
            <div><label class="block text-sm text-slate-400 mb-1">Влажность</label>
                <select name="humidity" class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2">
                    <option value="dry" @selected(old('humidity')==='dry')>Сухо</option>
                    <option value="normal" @selected(old('humidity')==='normal')>Нормально</option>
                    <option value="damp" @selected(old('humidity')==='damp')>Сыро</option>
                </select></div>
        </div>
        <div class="flex gap-4 text-sm">
            <label class="flex items-center gap-2"><input type="checkbox" name="has_people"
                                                          value="1" @checked(old('has_people'))> Есть люди</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="has_attic"
                                                          value="1" @checked(old('has_attic'))> Есть чердак</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="has_mirrors"
                                                          value="1" @checked(old('has_mirrors'))> Есть зеркала</label>
        </div>
        <div><label class="block text-sm text-slate-400 mb-1">Описание</label><textarea name="description" rows="2"
                                                                                        class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2">{{ old('description') }}</textarea>
        </div>
        <div class="flex gap-2 pt-2">
            <button class="bg-sky-600 hover:bg-sky-500 px-4 py-2 rounded text-sm">Сохранить</button>
            <a href="{{ route('locations.index') }}"
               class="px-4 py-2 rounded text-sm text-slate-400 hover:text-slate-200">Отмена</a>
        </div>
    </form>
@endsection

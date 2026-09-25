@extends('library.layout')
@section('title', 'Registrar libro')

@section('content')
<div class="mx-auto max-w-2xl">
    <h1 class="mb-1 text-3xl font-black text-[#003399]">Registrar libro</h1>
    <p class="mb-6 font-semibold text-slate-500">Alta por ISBN o código de barras — el lector USB escribe aquí y confirma con Enter.</p>

    <form method="POST" action="{{ route('biblioteca.libros.store') }}" class="card-premium rounded-3xl border-t-8 border-orange-500 p-8 shadow-lg">
        @csrf
        <div class="mb-4">
            <label class="lbl" for="isbn">ISBN (escanea o teclea)</label>
            <input class="field" id="isbn" name="isbn" value="{{ old('isbn') }}" required autofocus autocomplete="off" placeholder="978...">
        </div>
        <div class="mb-4">
            <label class="lbl" for="bar_code">Código de barras (opcional si aún no está impreso)</label>
            <input class="field" id="bar_code" name="bar_code" value="{{ old('bar_code') }}" autocomplete="off" placeholder="CBTA86...">
        </div>
        <div class="mb-4 grid gap-4 md:grid-cols-2">
            <div>
                <label class="lbl" for="titulo">Título</label>
                <input class="field" id="titulo" name="titulo" value="{{ old('titulo') }}" required>
            </div>
            <div>
                <label class="lbl" for="autor">Autor</label>
                <input class="field" id="autor" name="autor" value="{{ old('autor') }}" required>
            </div>
        </div>
        <div class="mb-6">
            <label class="lbl" for="existencias_totales">Ejemplares (total)</label>
            <input class="field" id="existencias_totales" name="existencias_totales" type="number" min="1" value="{{ old('existencias_totales', 1) }}" required>
        </div>
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('biblioteca.libros.index') }}" class="nav-link text-sm">Cancelar</a>
            <button type="submit" class="btn-primary rounded-full px-8 py-2.5 text-sm">Guardar</button>
        </div>
    </form>
</div>
@endsection

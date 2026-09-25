@extends('library.layout')
@section('title', 'Editar libro')

@section('content')
<div class="mx-auto max-w-2xl">
    <h1 class="mb-1 text-3xl font-black text-[#003399]">Editar libro</h1>
    <p class="mb-6 font-semibold text-slate-500">ISBN <span class="font-black text-slate-700">{{ $book->isbn() }}</span> · {{ $book->barCode() ?? 'sin código de barras' }}</p>

    <form method="POST" action="{{ route('biblioteca.libros.update', $book->id()) }}" class="card-premium rounded-3xl border-t-8 border-orange-500 p-8 shadow-lg">
        @csrf @method('PUT')
        <div class="mb-4 grid gap-4 md:grid-cols-2">
            <div>
                <label class="lbl" for="titulo">Título</label>
                <input class="field" id="titulo" name="titulo" value="{{ old('titulo', $book->titulo()) }}" required>
            </div>
            <div>
                <label class="lbl" for="autor">Autor</label>
                <input class="field" id="autor" name="autor" value="{{ old('autor', $book->autor()) }}" required>
            </div>
        </div>
        <div class="mb-6">
            <label class="lbl" for="existencias_totales">Ejemplares (total)</label>
            <input class="field" id="existencias_totales" name="existencias_totales" type="number" min="1" value="{{ old('existencias_totales', $book->totalCopies()) }}" required>
            <p class="mt-1 text-xs font-semibold text-slate-400">Prestados ahora: {{ $book->totalCopies() - $book->availableCopies() }}. El total no puede bajar de esa cifra.</p>
        </div>
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('biblioteca.libros.index') }}" class="nav-link text-sm">Cancelar</a>
            <button type="submit" class="btn-primary rounded-full px-8 py-2.5 text-sm">Guardar cambios</button>
        </div>
    </form>
</div>
@endsection

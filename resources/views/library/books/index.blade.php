@extends('library.layout')
@section('title', 'Libros')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-3xl font-black text-[#003399]">Libros</h1>
        <p class="font-semibold text-slate-500">Catálogo de la biblioteca escolar.</p>
    </div>
    <a href="{{ route('biblioteca.libros.create') }}" class="btn-primary rounded-full px-6 py-2.5 text-sm">+ Registrar libro</a>
</div>

<form method="GET" action="{{ route('biblioteca.libros.index') }}" class="card-premium mb-6 flex flex-wrap items-end gap-3 rounded-3xl p-5 shadow-lg">
    <div class="min-w-52 flex-1">
        <label class="lbl" for="q">Buscar (título, autor, ISBN o código)</label>
        <input class="field" id="q" name="q" value="{{ $filters['q'] }}" autocomplete="off">
    </div>
    <div>
        <label class="lbl" for="estado">Estado</label>
        <select class="field" id="estado" name="estado">
            <option value="">Todos</option>
            <option value="activo" @selected($filters['estado'] === 'activo')>Activo</option>
            <option value="baja" @selected($filters['estado'] === 'baja')>Baja</option>
        </select>
    </div>
    <button type="submit" class="btn-primary rounded-full px-6 py-2.5 text-sm">Filtrar</button>
    <a href="{{ route('biblioteca.libros.index') }}" class="nav-link text-sm">Limpiar</a>
    <button type="submit" formaction="{{ route('biblioteca.libros.exportar') }}" name="formato" value="pdf" class="rounded-full border-2 border-[#003399] px-5 py-1.5 text-xs font-black text-[#003399] transition hover:bg-[#003399] hover:text-white">PDF</button>
    <button type="submit" formaction="{{ route('biblioteca.libros.exportar') }}" name="formato" value="xlsx" class="rounded-full border-2 border-green-700 px-5 py-1.5 text-xs font-black text-green-700 transition hover:bg-green-700 hover:text-white">Excel</button>
</form>

<div class="card-premium overflow-hidden rounded-3xl shadow-lg">
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="bg-[#003399] text-xs uppercase tracking-wider text-white">
                <th class="px-5 py-3">Título / Autor</th>
                <th class="px-5 py-3">ISBN</th>
                <th class="px-5 py-3">Código</th>
                <th class="px-5 py-3 text-center">Disponibles</th>
                <th class="px-5 py-3 text-center">Estado</th>
                <th class="px-5 py-3 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="font-semibold text-slate-700">
            @forelse ($paginator->items() as $book)
                <tr class="border-t border-slate-100">
                    <td class="px-5 py-3">
                        <span class="font-black text-slate-900">{{ $book->titulo() }}</span><br>
                        <span class="text-xs text-slate-500">{{ $book->autor() }}</span>
                    </td>
                    <td class="px-5 py-3">{{ $book->isbn() }}</td>
                    <td class="px-5 py-3">{{ $book->barCode() ?? '—' }}</td>
                    <td class="px-5 py-3 text-center">{{ $book->availableCopies() }} / {{ $book->totalCopies() }}</td>
                    <td class="px-5 py-3 text-center">
                        @if ($book->isActive())
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-black text-green-700">ACTIVO</span>
                        @else
                            <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-black text-slate-500">BAJA</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('biblioteca.libros.edit', $book->id()) }}" class="nav-link text-sm">Editar</a>
                        @if ($book->isActive())
                            <form method="POST" action="{{ route('biblioteca.libros.destroy', $book->id()) }}" class="inline" onsubmit="return confirm('¿Dar de baja este libro?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ms-3 text-sm font-bold text-red-600 hover:text-red-800">Baja</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('biblioteca.libros.reactivar', $book->id()) }}" class="inline">
                                @csrf
                                <button type="submit" class="ms-3 text-sm font-bold text-green-600 hover:text-green-800">Reactivar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">Sin libros registrados. Usa «Registrar libro» o escanea un código.</td></tr>
            @endforelse
        </tbody>
    </table>
    @include('library._pagination', ['paginator' => $paginator])
</div>
@endsection

@extends('library.layout')
@section('title', 'Miembros')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-3xl font-black text-[#003399]">Miembros</h1>
        <p class="font-semibold text-slate-500">Alumnos, docentes, personal y grupos.</p>
    </div>
    <a href="{{ route('biblioteca.miembros.create') }}" class="btn-primary rounded-full px-6 py-2.5 text-sm">+ Registrar miembro</a>
</div>

<form method="GET" action="{{ route('biblioteca.miembros.index') }}" class="card-premium mb-6 flex flex-wrap items-end gap-3 rounded-3xl p-5 shadow-lg">
    <div class="min-w-52 flex-1">
        <label class="lbl" for="q">Buscar (nombre, identificador o grupo)</label>
        <input class="field" id="q" name="q" value="{{ $filters['q'] }}" autocomplete="off">
    </div>
    <div>
        <label class="lbl" for="tipo">Tipo</label>
        <select class="field" id="tipo" name="tipo">
            <option value="">Todos</option>
            @foreach ($tipos as $tipo)
                <option value="{{ $tipo }}" @selected($filters['tipo'] === $tipo)>{{ ucfirst($tipo) }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn-primary rounded-full px-6 py-2.5 text-sm">Filtrar</button>
    <a href="{{ route('biblioteca.miembros.index') }}" class="nav-link text-sm">Limpiar</a>
    <button type="submit" formaction="{{ route('biblioteca.miembros.exportar') }}" name="formato" value="pdf" class="rounded-full border-2 border-[#003399] px-5 py-1.5 text-xs font-black text-[#003399] transition hover:bg-[#003399] hover:text-white">PDF</button>
    <button type="submit" formaction="{{ route('biblioteca.miembros.exportar') }}" name="formato" value="xlsx" class="rounded-full border-2 border-green-700 px-5 py-1.5 text-xs font-black text-green-700 transition hover:bg-green-700 hover:text-white">Excel</button>
</form>

<div class="card-premium overflow-hidden rounded-3xl shadow-lg">
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="bg-[#003399] text-xs uppercase tracking-wider text-white">
                <th class="px-5 py-3">Nombre</th>
                <th class="px-5 py-3 text-center">Tipo</th>
                <th class="px-5 py-3">Identificador</th>
                <th class="px-5 py-3">Grupo escolar</th>
            </tr>
        </thead>
        <tbody class="font-semibold text-slate-700">
            @forelse ($paginator->items() as $member)
                <tr class="border-t border-slate-100">
                    <td class="px-5 py-3 font-black text-slate-900">{{ $member->nombre() }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-[#003399]">{{ ucfirst($member->tipo()) }}</span>
                    </td>
                    <td class="px-5 py-3">{{ $member->identificador() }}</td>
                    <td class="px-5 py-3">{{ $member->grupoEscolar() ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-10 text-center text-slate-400">Sin miembros registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
    @include('library._pagination', ['paginator' => $paginator])
</div>
@endsection

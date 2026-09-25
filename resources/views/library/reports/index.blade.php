@extends('library.layout')
@section('title', 'Reportes')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-black text-[#003399]">Reportes</h1>
    <p class="font-semibold text-slate-500">Control escolar: filtra y exporta a PDF o Excel.</p>
</div>

<form method="GET" action="{{ route('biblioteca.reportes.index') }}" class="card-premium mb-6 rounded-3xl p-6 shadow-lg">
    <div class="grid gap-4 md:grid-cols-5">
        <div class="md:col-span-2">
            <label class="lbl" for="tipo">Reporte</label>
            <select class="field" id="tipo" name="tipo" onchange="toggleMiembro()">
                @foreach ($types as $key => $title)
                    <option value="{{ $key }}" @selected(($filters['tipo'] ?? 'activos') === $key)>{{ $title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="lbl" for="desde">Desde</label>
            <input class="field" id="desde" name="desde" type="date" value="{{ $filters['desde'] ?? '' }}">
        </div>
        <div>
            <label class="lbl" for="hasta">Hasta</label>
            <input class="field" id="hasta" name="hasta" type="date" value="{{ $filters['hasta'] ?? '' }}">
        </div>
        <div id="miembro-wrap">
            <label class="lbl" for="member_id">Miembro (solo historial)</label>
            <select class="field" id="member_id" name="member_id">
                <option value="">— Selecciona —</option>
                @foreach ($members as $member)
                    <option value="{{ $member->id() }}" @selected(($filters['member_id'] ?? null) == $member->id())>{{ $member->nombre() }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div id="tipo-wrap" class="mt-4 grid gap-4 md:grid-cols-5">
        <div>
            <label class="lbl" for="tipo_miembro">Filtrar por tipo (solo no devueltos)</label>
            <select class="field" id="tipo_miembro" name="tipo_miembro">
                <option value="">Todos</option>
                @foreach (['alumno' => 'Alumno', 'docente' => 'Docente', 'personal' => 'Personal', 'grupo' => 'Grupo'] as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['tipo_miembro'] ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="mt-4 flex flex-wrap items-center gap-3">
        <button type="submit" class="btn-primary rounded-full px-8 py-2.5 text-sm">Generar</button>
        <button type="submit" formaction="{{ route('biblioteca.reportes.exportar') }}" formmethod="GET" name="formato" value="pdf" class="rounded-full border-2 border-[#003399] px-6 py-2 text-sm font-black text-[#003399] transition hover:bg-[#003399] hover:text-white">PDF</button>
        <button type="submit" formaction="{{ route('biblioteca.reportes.exportar') }}" formmethod="GET" name="formato" value="xlsx" class="rounded-full border-2 border-green-700 px-6 py-2 text-sm font-black text-green-700 transition hover:bg-green-700 hover:text-white">Excel</button>
    </div>
</form>

<div class="card-premium overflow-hidden rounded-3xl border-t-8 border-orange-500 shadow-lg">
    <div class="px-6 pt-5">
        <h2 class="text-xl font-black text-[#003399]">{{ $result['title'] }}</h2>
        <p class="mb-4 text-sm font-semibold text-slate-500">{{ $result['summary'] }}</p>
    </div>
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="bg-[#003399] text-xs uppercase tracking-wider text-white">
                @foreach ($result['headings'] as $heading)
                    <th class="px-5 py-3">{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="font-semibold text-slate-700">
            @forelse ($pageRows as $row)
                <tr class="border-t border-slate-100">
                    @foreach ($row as $cell)
                        <td class="px-5 py-2.5">{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($result['headings']) }}" class="px-5 py-10 text-center text-slate-400">Sin resultados para esos filtros.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $paginator->links() }}</div>
</div>

<script>
function toggleMiembro() {
    const tipo = document.getElementById('tipo').value;
    document.getElementById('miembro-wrap').style.opacity = tipo === 'historial-miembro' ? '1' : '0.4';
    document.getElementById('tipo-wrap').style.opacity = tipo === 'no-devueltos' ? '1' : '0.4';
}
toggleMiembro();
</script>
@endsection

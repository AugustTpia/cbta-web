@extends('library.layout')
@section('title', 'Registrar miembro')

@section('content')
<div class="mx-auto max-w-2xl">
    <h1 class="mb-1 text-3xl font-black text-[#003399]">Registrar miembro</h1>
    <p class="mb-6 font-semibold text-slate-500">Alumno, docente, personal o grupo (clase completa).</p>

    <form method="POST" action="{{ route('biblioteca.miembros.store') }}" class="card-premium rounded-3xl border-t-8 border-orange-500 p-8 shadow-lg">
        @csrf
        <div class="mb-4">
            <label class="lbl" for="tipo">Tipo</label>
            <select class="field" id="tipo" name="tipo" required onchange="toggleGrupo()">
                @foreach ($tipos as $tipo)
                    <option value="{{ $tipo }}" @selected(old('tipo') === $tipo)>{{ ucfirst($tipo) }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="lbl" for="nombre">Nombre</label>
            <input class="field" id="nombre" name="nombre" value="{{ old('nombre') }}" required autofocus placeholder="Nombre completo o nombre del grupo">
        </div>
        <div class="mb-4 grid gap-4 md:grid-cols-2">
            <div>
                <label class="lbl" for="identificador">Identificador (matrícula/clave)</label>
                <input class="field" id="identificador" name="identificador" value="{{ old('identificador') }}" required autocomplete="off">
            </div>
            <div id="grupo-wrap">
                <label class="lbl" for="grupo_escolar">Grupo escolar</label>
                <input class="field" id="grupo_escolar" name="grupo_escolar" value="{{ old('grupo_escolar') }}" placeholder='Ej. "3° B"'>
            </div>
        </div>
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('biblioteca.miembros.index') }}" class="nav-link text-sm">Cancelar</a>
            <button type="submit" class="btn-primary rounded-full px-8 py-2.5 text-sm">Guardar</button>
        </div>
    </form>
</div>

<script>
function toggleGrupo() {
    const tipo = document.getElementById('tipo').value;
    const wrap = document.getElementById('grupo-wrap');
    const input = document.getElementById('grupo_escolar');
    const needs = (tipo === 'alumno' || tipo === 'grupo');
    wrap.style.opacity = needs ? '1' : '0.4';
    input.required = needs;
}
toggleGrupo();
</script>
@endsection

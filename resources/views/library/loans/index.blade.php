@extends('library.layout')
@section('title', 'Panel de préstamos')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-3xl font-black text-[#003399]">Préstamos</h1>
        <p class="font-semibold text-slate-500">Activos y vencidos de un vistazo.</p>
    </div>
    <a href="{{ route('biblioteca.prestamos.create') }}" class="btn-primary rounded-full px-6 py-2.5 text-sm">+ Nuevo préstamo</a>
</div>

<form method="GET" action="{{ route('biblioteca.dashboard') }}" class="card-premium mb-6 flex flex-wrap items-end gap-3 rounded-3xl p-5 shadow-lg">
    <div class="min-w-52 flex-1">
        <label class="lbl" for="miembro">Filtrar por miembro</label>
        <input class="field" id="miembro" name="miembro" value="{{ $memberTerm }}" autocomplete="off" placeholder="Nombre de quien lo tiene">
    </div>
    <button type="submit" class="btn-primary rounded-full px-6 py-2.5 text-sm">Filtrar</button>
    <a href="{{ route('biblioteca.dashboard') }}" class="nav-link text-sm">Limpiar</a>
    <button type="submit" formaction="{{ route('biblioteca.prestamos.exportar') }}" name="formato" value="pdf" class="rounded-full border-2 border-[#003399] px-5 py-1.5 text-xs font-black text-[#003399] transition hover:bg-[#003399] hover:text-white">PDF</button>
    <button type="submit" formaction="{{ route('biblioteca.prestamos.exportar') }}" name="formato" value="xlsx" class="rounded-full border-2 border-green-700 px-5 py-1.5 text-xs font-black text-green-700 transition hover:bg-green-700 hover:text-white">Excel</button>
</form>

@if (count($overdue) > 0)
    <div class="card-premium mb-8 overflow-hidden rounded-3xl border-t-8 border-red-500 shadow-lg">
        <h2 class="px-6 pt-5 text-xl font-black text-red-600">⚠️ Vencidos ({{ $overduePaginator->total() }})</h2>
        @include('library.loans._table', ['loans' => $overdue, 'showOverdue' => true])
        @include('library._pagination', ['paginator' => $overduePaginator, 'pageParam' => 'pagV'])
    </div>
@endif

<div class="card-premium overflow-hidden rounded-3xl border-t-8 border-orange-500 shadow-lg">
    <h2 class="px-6 pt-5 text-xl font-black text-[#003399]">Activos ({{ $activePaginator->total() }})</h2>
    @include('library.loans._table', ['loans' => $active, 'showOverdue' => false])
    @include('library._pagination', ['paginator' => $activePaginator, 'pageParam' => 'pagA'])
</div>
@endsection

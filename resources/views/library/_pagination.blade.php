{{-- Espera $paginator (PaginatedResult) y $pageParam (nombre del query param, default 'page'). --}}
@if ($paginator->hasPages())
    <div class="flex flex-wrap items-center justify-between gap-2 px-6 py-4 text-sm font-bold text-slate-500">
        <span>Total: {{ $paginator->total() }} · Página {{ $paginator->page() }} de {{ $paginator->lastPage() }}</span>
        <div class="flex gap-2">
            @php($param = $pageParam ?? 'page')
            @if ($paginator->page() > 1)
                <a href="{{ request()->fullUrlWithQuery([$param => $paginator->page() - 1]) }}" class="rounded-full border border-[#003399] px-4 py-1 text-[#003399] transition hover:bg-[#003399] hover:text-white">← Anterior</a>
            @endif
            @if ($paginator->page() < $paginator->lastPage())
                <a href="{{ request()->fullUrlWithQuery([$param => $paginator->page() + 1]) }}" class="rounded-full border border-[#003399] px-4 py-1 text-[#003399] transition hover:bg-[#003399] hover:text-white">Siguiente →</a>
            @endif
        </div>
    </div>
@elseif ($paginator->total() > 0)
    <div class="px-6 py-3 text-sm font-bold text-slate-400">Total: {{ $paginator->total() }}</div>
@endif

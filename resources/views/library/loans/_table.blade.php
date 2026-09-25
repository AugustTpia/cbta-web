<table class="w-full text-left text-sm">
    <thead>
        <tr class="bg-[#003399] text-xs uppercase tracking-wider text-white">
            <th class="px-5 py-3">#</th>
            <th class="px-5 py-3">Quién lo tiene</th>
            <th class="px-5 py-3">Libros</th>
            <th class="px-5 py-3">Préstamo</th>
            <th class="px-5 py-3">Devolución</th>
            @if ($showOverdue)
                <th class="px-5 py-3 text-center">Atraso</th>
            @endif
        </tr>
    </thead>
    <tbody class="font-semibold text-slate-700">
        @forelse ($loans as $loan)
            <tr class="border-t border-slate-100 align-top">
                <td class="px-5 py-3 font-black text-slate-900">{{ $loan['id'] }}</td>
                <td class="px-5 py-3">
                    <span class="font-black text-slate-900">{{ $loan['miembro'] }}</span><br>
                    <span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-black text-[#003399]">{{ ucfirst($loan['tipo']) }}</span>
                    @if ($loan['grupo'])
                        <span class="text-xs text-slate-500">{{ $loan['grupo'] }}</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <ul class="space-y-2">
                        @foreach ($loan['items'] as $item)
                            <li class="flex items-center justify-between gap-2 {{ $item['devuelto'] ? 'text-slate-400 line-through' : 'text-slate-800' }}">
                                <span>
                                    {{ $item['titulo'] }}
                                    @if ($item['devuelto'])
                                        <span class="text-xs no-underline">✓ {{ $item['fecha_devolucion'] }}</span>
                                    @endif
                                </span>
                                @if (! $item['devuelto'])
                                    <form method="POST" action="{{ route('biblioteca.prestamos.devolver', $loan['id']) }}" onsubmit="return confirm('¿Registrar devolución de {{ $item['titulo'] }}?');">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $item['book_id'] }}">
                                        <button type="submit" class="shrink-0 rounded-full bg-[#003399] px-3 py-1 text-xs font-black text-white transition hover:bg-[#FF6600]">Devolver</button>
                                    </form>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td class="px-5 py-3">{{ $loan['prestamo'] }}</td>
                <td class="px-5 py-3">{{ $loan['devolucion'] }}</td>
                @if ($showOverdue)
                    <td class="px-5 py-3 text-center"><span class="rounded-full bg-red-100 px-3 py-1 text-xs font-black text-red-700">{{ $loan['atraso'] }} días</span></td>
                @endif
            </tr>
        @empty
            <tr><td colspan="{{ $showOverdue ? 6 : 5 }}" class="px-5 py-10 text-center text-slate-400">Nada por aquí.</td></tr>
        @endforelse
    </tbody>
</table>

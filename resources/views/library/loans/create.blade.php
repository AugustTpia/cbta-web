@extends('library.layout')
@section('title', 'Nuevo préstamo')

@section('content')
<div class="mx-auto max-w-3xl">
    <h1 class="mb-1 text-3xl font-black text-[#003399]">Nuevo préstamo</h1>
    <p class="mb-6 font-semibold text-slate-500">Elige al miembro, escanea los libros y fija la fecha de devolución.</p>

    <form method="POST" action="{{ route('biblioteca.prestamos.store') }}" class="card-premium rounded-3xl border-t-8 border-orange-500 p-8 shadow-lg">
        @csrf
        <div class="mb-4 grid gap-4 md:grid-cols-2">
            <div>
                <label class="lbl" for="member-search">Miembro: busca por nombre, matrícula o grupo y grado</label>
                <input type="hidden" id="member_id" name="member_id" value="{{ old('member_id') }}">
                <div id="member-picked" class="hidden items-center justify-between gap-2 rounded-xl border-2 border-[#003399] bg-blue-50 px-4 py-2.5">
                    <div>
                        <p id="member-name" class="font-black text-[#003399]"></p>
                        <p id="member-detail" class="text-xs font-semibold text-slate-500"></p>
                    </div>
                    <button type="button" onclick="clearMember()" class="text-sm font-bold text-red-600">Cambiar</button>
                </div>
                <div id="member-box">
                    <input class="field" id="member-search" type="text" autocomplete="off" placeholder='Ej. "Juan", "A2024" o "3° B"'>
                    <div id="member-results" class="mt-1 hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg"></div>
                    <p id="member-msg" class="mt-1 hidden text-xs font-bold text-red-600"></p>
                </div>
            </div>
            <div>
                <label class="lbl" for="fecha_estimada_devolucion">Fecha estimada de devolución *</label>
                <input class="field" id="fecha_estimada_devolucion" name="fecha_estimada_devolucion" type="date" value="{{ old('fecha_estimada_devolucion') }}" required>
            </div>
        </div>

        <div class="mb-2">
            <label class="lbl" for="scan">Escanear o buscar libro (ISBN o código)</label>
            <div class="flex gap-2">
                <input class="field" id="scan" type="text" autocomplete="off" autofocus placeholder="Escanea y presiona Enter">
                <button type="button" onclick="addScanned()" class="btn-primary rounded-xl px-5 text-sm">Agregar</button>
            </div>
            <p id="scan-msg" class="mt-1 hidden text-xs font-bold text-red-600"></p>
        </div>

        <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200">
            <table class="w-full text-left text-sm">
                <thead><tr class="bg-slate-50 text-xs uppercase text-slate-500">
                    <th class="px-4 py-2">Libro</th><th class="px-4 py-2 text-center">Disponibles</th><th class="px-4 py-2 text-right">Quitar</th>
                </tr></thead>
                <tbody id="loan-books" class="font-semibold text-slate-700">
                    <tr id="loan-empty"><td colspan="3" class="px-4 py-6 text-center text-slate-400">Sin libros. Escanea el primero.</td></tr>
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('biblioteca.dashboard') }}" class="nav-link text-sm">Cancelar</a>
            <button type="submit" class="btn-primary rounded-full px-8 py-2.5 text-sm">Prestar</button>
        </div>
    </form>
</div>

<script>
let searchTimer = null;
const searchInput = document.getElementById('member-search');
searchInput.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(searchMembers, 250);
});
async function searchMembers() {
    const box = document.getElementById('member-results');
    const msg = document.getElementById('member-msg');
    const term = searchInput.value.trim();
    msg.classList.add('hidden');
    if (term.length < 2) { box.classList.add('hidden'); box.innerHTML = ''; return; }
    const res = await fetch("{{ route('biblioteca.miembros.buscar') }}?q=" + encodeURIComponent(term));
    const data = await res.json();
    box.innerHTML = '';
    if (data.results.length === 0) {
        msg.textContent = 'Sin resultados para "' + term + '"';
        msg.classList.remove('hidden');
        box.classList.add('hidden');
        return;
    }
    data.results.forEach(m => {
        const row = document.createElement('button');
        row.type = 'button';
        row.className = 'flex w-full items-center justify-between gap-2 px-4 py-2 text-left hover:bg-blue-50';
        row.innerHTML = `<span><span class="font-black text-slate-900">${m.nombre}</span> <span class="text-xs text-slate-500">${m.identificador}${m.grupo ? ' · ' + m.grupo : ''}</span></span><span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-black text-[#003399]">${m.tipo}</span>`;
        row.onclick = () => pickMember(m);
        box.appendChild(row);
    });
    box.classList.remove('hidden');
}
function pickMember(m) {
    document.getElementById('member_id').value = m.id;
    document.getElementById('member-name').textContent = m.nombre;
    document.getElementById('member-detail').textContent = m.tipo + ' · ' + m.identificador + (m.grupo ? ' · ' + m.grupo : '');
    document.getElementById('member-picked').classList.remove('hidden');
    document.getElementById('member-picked').classList.add('flex');
    document.getElementById('member-box').classList.add('hidden');
}
function clearMember() {
    document.getElementById('member_id').value = '';
    document.getElementById('member-picked').classList.add('hidden');
    document.getElementById('member-picked').classList.remove('flex');
    document.getElementById('member-box').classList.remove('hidden');
    searchInput.value = '';
    searchInput.focus();
}
document.querySelector('form').addEventListener('submit', e => {
    if (!document.getElementById('member_id').value) {
        e.preventDefault();
        const msg = document.getElementById('member-msg');
        msg.textContent = 'Primero busca y elige al miembro.';
        msg.classList.remove('hidden');
        searchInput.focus();
    }
});
const added = new Set();
async function addScanned() {
    const input = document.getElementById('scan');
    const msg = document.getElementById('scan-msg');
    const code = input.value.trim();
    msg.classList.add('hidden');
    if (!code) return;
    const res = await fetch("{{ route('biblioteca.libros.buscar') }}?q=" + encodeURIComponent(code));
    const data = await res.json();
    input.value = '';
    input.focus();
    if (!data.found) { msg.textContent = 'No encontrado: ' + code; msg.classList.remove('hidden'); return; }
    const b = data.book;
    if (added.has(b.id)) { msg.textContent = 'Ya está en la lista: ' + b.titulo; msg.classList.remove('hidden'); return; }
    if (!b.activo) { msg.textContent = 'Dado de baja: ' + b.titulo; msg.classList.remove('hidden'); return; }
    if (b.disponibles <= 0) { msg.textContent = 'Sin existencias: ' + b.titulo; msg.classList.remove('hidden'); return; }
    added.add(b.id);
    document.getElementById('loan-empty')?.remove();
    const row = document.createElement('tr');
    row.id = 'book-' + b.id;
    row.className = 'border-t border-slate-100';
    row.innerHTML = `<td class="px-4 py-2"><span class="font-black text-slate-900">${b.titulo}</span><input type="hidden" name="book_ids[]" value="${b.id}"></td><td class="px-4 py-2 text-center">${b.disponibles}</td><td class="px-4 py-2 text-right"><button type="button" onclick="removeBook(${b.id})" class="text-sm font-bold text-red-600">Quitar</button></td>`;
    document.getElementById('loan-books').appendChild(row);
}
function removeBook(id) {
    added.delete(id);
    document.getElementById('book-' + id)?.remove();
}
document.getElementById('scan').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); addScanned(); } });
</script>
@endsection

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Préstamos · CBTA 86</title>
    <style>
        body { font-family: sans-serif; color: #0f172a; }
        h1 { color: #003399; font-size: 20px; margin-bottom: 0; }
        h2 { color: #003399; font-size: 15px; margin: 18px 0 0; }
        p.summary { color: #64748b; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 8px; }
        th { background: #003399; color: #fff; text-align: left; padding: 6px; }
        td { border-bottom: 1px solid #e2e8f0; padding: 6px; }
        .footer { margin-top: 16px; font-size: 10px; color: #94a3b8; }
    </style>
</head>
<body>
    <h1>Préstamos de biblioteca</h1>
    <p class="summary">CBTA 86 · Biblioteca escolar · {{ date('d/m/Y H:i') }}</p>

    <h2>Vencidos ({{ count($overdue['rows']) }})</h2>
    <table>
        <thead><tr>@foreach ($overdue['headings'] as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
        <tbody>
            @foreach ($overdue['rows'] as $row)
                <tr>@foreach ($row as $cell)<td>{{ $cell }}</td>@endforeach</tr>
            @endforeach
        </tbody>
    </table>

    <h2>Activos ({{ count($active['rows']) }})</h2>
    <table>
        <thead><tr>@foreach ($active['headings'] as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
        <tbody>
            @foreach ($active['rows'] as $row)
                <tr>@foreach ($row as $cell)<td>{{ $cell }}</td>@endforeach</tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">Generado por el Sistema de Biblioteca Escolar · CBTA 86</p>
</body>
</html>

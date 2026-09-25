<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $result['title'] }} · CBTA 86</title>
    <style>
        body { font-family: sans-serif; color: #0f172a; }
        h1 { color: #003399; font-size: 20px; margin-bottom: 0; }
        p.summary { color: #64748b; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 12px; }
        th { background: #003399; color: #fff; text-align: left; padding: 6px; }
        td { border-bottom: 1px solid #e2e8f0; padding: 6px; }
        .footer { margin-top: 16px; font-size: 10px; color: #94a3b8; }
    </style>
</head>
<body>
    <h1>{{ $result['title'] }}</h1>
    <p class="summary">CBTA 86 · Biblioteca escolar · {{ date('d/m/Y H:i') }}</p>
    <p class="summary">{{ $result['summary'] }}</p>
    <table>
        <thead>
            <tr>
                @foreach ($result['headings'] as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($result['rows'] as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="footer">Generado por el Sistema de Biblioteca Escolar · CBTA 86</p>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { margin-bottom: 20px; border-bottom: 1px solid #000; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #eee; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $title }}</h2>
        <p>Situation au : {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
        <p>Organisation : {{ auth()->user()->tenant->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Caisse</th>
                <th class="right">Solde Original</th>
                <th class="right">Contre-valeur ({{ auth()->user()->tenant->default_currency }})</th>
                <th class="right">Taux</th>
            </tr>
        </thead>
        <tbody>
            @foreach($balances as $row)
            <tr>
                <td>{{ $row['cashbox'] }}</td>
                <td class="right">{{ number_format($row['balance'], 2) }} {{ $row['currency'] }}</td>
                <td class="right">{{ number_format($row['balance_base'], 2) }}</td>
                <td class="right">{{ number_format($row['rate_used'], 4) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

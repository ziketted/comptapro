<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: sans-serif; }
        .container { border: 1px solid #ccc; padding: 20px; }
        .row { margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .label { font-weight: bold; }
        .value { float: right; }
        .net-result { font-size: 1.2em; font-weight: bold; margin-top: 20px; border-top: 2px solid #000; padding-top: 10px; }
        .positive { color: green; }
        .negative { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h2>{{ $title }}</h2>
        <p>Période: 
            @if($filters['start_date']) {{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }} @else Début @endif 
            - 
            @if($filters['end_date']) {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }} @else Fin @endif
        </p>

        <div class="row">
            <span class="label">Total Recettes</span>
            <span class="value">{{ number_format($total_income, 2) }} {{ $base_currency }}</span>
        </div>

        <div class="row">
            <span class="label">Total Dépenses</span>
            <span class="value">{{ number_format($total_expense, 2) }} {{ $base_currency }}</span>
        </div>

        <div class="net-result">
            <span class="label">RÉSULTAT NET</span>
            <span class="value {{ $net_result >= 0 ? 'positive' : 'negative' }}">
                {{ $net_result >= 0 ? '+' : '' }}{{ number_format($net_result, 2) }} {{ $base_currency }}
            </span>
        </div>
    </div>
</body>
</html>

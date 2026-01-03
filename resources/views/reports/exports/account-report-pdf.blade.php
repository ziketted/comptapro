<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #ccc; padding-bottom: 10px; }
        .title { font-size: 16px; font-weight: bold; }
        .date { float: right; font-size: 10px; }
        .section { margin-bottom: 25px; }
        .section-title { font-size: 14px; font-weight: bold; border-bottom: 1px solid #eee; margin-bottom: 10px; padding-bottom: 5px; color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #eee; padding: 5px; text-align: left; }
        th { background-color: #f9f9f9; }
        .amount { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="date">Date: {{ $date }}</div>
        <div class="title">{{ $title }}</div>
        <div>{{ auth()->user()->tenant->name }}</div>
    </div>

    <!-- Income Section -->
    <div class="section">
        <div class="section-title" style="color: #059669;">RECETTES ({{ $baseCurrency }})</div>
        <table>
            <thead>
                <tr>
                    <th width="20%">Numéro</th>
                    <th width="50%">Compte</th>
                    <th width="30%" class="amount">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($income as $row)
                <tr>
                    <td>{{ $row['account']->account_number }}</td>
                    <td>{{ $row['account']->name }}</td>
                    <td class="amount">{{ number_format($row['total'], 2) }}</td>
                </tr>
                @endforeach
                @if(count($income)==0)
                <tr><td colspan="3" style="text-align:center; color:#999;">Aucune donnée</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Expense Section -->
    <div class="section">
        <div class="section-title" style="color: #dc2626;">DEPENSES ({{ $baseCurrency }})</div>
        <table>
            <thead>
                <tr>
                    <th width="20%">Numéro</th>
                    <th width="50%">Compte</th>
                    <th width="30%" class="amount">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expense as $row)
                <tr>
                    <td>{{ $row['account']->account_number }}</td>
                    <td>{{ $row['account']->name }}</td>
                    <td class="amount">{{ number_format($row['total'], 2) }}</td>
                </tr>
                @endforeach
                 @if(count($expense)==0)
                <tr><td colspan="3" style="text-align:center; color:#999;">Aucune donnée</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>

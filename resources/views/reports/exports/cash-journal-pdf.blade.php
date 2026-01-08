<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        .header { margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; }
        .date { float: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f5f5f5; }
        .amount { text-align: right; }
        .type-INCOME { color: green; }
        .type-EXPENSE { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <div class="date">Généré le: {{ $date }}</div>
        <div class="title">{{ $title }}</div>
        <div>Organisation: {{ auth()->user()->tenant->name }}</div>
    </div>

    @if(!empty($filters))
    <div style="margin-bottom: 15px; background: #f9f9f9; padding: 5px;">
        <strong>Filtres:</strong>
        @if($filters['start_date'] ?? false) Du: {{ $filters['start_date'] }} @endif
        @if($filters['end_date'] ?? false) Au: {{ $filters['end_date'] }} @endif
        @if($filters['type'] ?? false) Type: {{ $filters['type'] }} @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Compte</th>
                <th>Tiers</th>
                <th>Description</th>
                <th class="amount">Montant (Origine)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($operations as $op)
            <tr>
                <td>{{ $op->operation_date->format('d/m/Y') }}</td>
                <td class="type-{{ $op->type }}">{{ $op->type_label }}</td>
                <td>{{ $op->account ? $op->account->name : '-' }}</td>
                <td>{{ $op->beneficiary ? $op->beneficiary->name : '-' }}</td>
                <td>{{ $op->description }}</td>
                <td class="amount">{{ number_format($op->original_amount, 2) }} {{ $op->currency->code }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <h3 style="border-bottom: 2px solid #333; padding-bottom: 5px; font-size: 12px; text-transform: uppercase;">Résumé des Soldes</h3>
        @foreach($totals as $total)
        <div style="margin-bottom: 15px; border: 1px solid #eee; padding: 10px; background-color: #fcfcfc;">
            <table style="margin-top: 0; border: none;">
                <tr style="border: none;">
                    <td style="border: none; width: 33%;">
                        <div style="color: #666; font-size: 8px; text-transform: uppercase; margin-bottom: 3px;">Total Recettes</div>
                        <div style="font-size: 12px; font-weight: bold; color: green;">+{{ number_format($total['income'], 2) }} {{ $total['currency'] }}</div>
                    </td>
                    <td style="border: none; width: 33%;">
                        <div style="color: #666; font-size: 8px; text-transform: uppercase; margin-bottom: 3px;">Total Dépenses</div>
                        <div style="font-size: 12px; font-weight: bold; color: red;">-{{ number_format($total['expense'], 2) }} {{ $total['currency'] }}</div>
                    </td>
                    <td style="border: none; width: 34%;">
                        <div style="color: #666; font-size: 8px; text-transform: uppercase; margin-bottom: 3px;">Solde Net</div>
                        <div style="font-size: 12px; font-weight: bold; color: {{ $total['balance'] >= 0 ? '#0000ff' : '#ff0000' }};">
                            {{ $total['balance'] >= 0 ? '+' : '' }}{{ number_format($total['balance'], 2) }} {{ $total['currency'] }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        @endforeach
    </div>
</body>
</html>

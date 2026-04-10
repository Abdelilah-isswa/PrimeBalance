<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bill #{{ $bill->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .header { text-align: center; margin-bottom: 30px; }
        .company-info { margin-bottom: 20px; }
        .bill-details { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f5f5f5; }
        .total { font-weight: bold; font-size: 16px; }
        .status { padding: 5px 10px; border-radius: 4px; display: inline-block; }
        .status-paid { background: #e8f5e9; color: #2e7d32; }
        .status-unpaid { background: #fff9c4; color: #f57f17; }
        .status-partial { background: #e3f2fd; color: #1565c0; }
        .status-cancelled { background: #ffebee; color: #c62828; }
    </style>
</head>
<body>
    <div class="header">
        <h1>BILL</h1>
        <p>Bill #{{ $bill->id }}</p>
    </div>

    <div class="company-info">
        <h3>{{ $company->name }}</h3>
        <p>{{ $company->address }}</p>
        <p>Date: {{ $bill->created_at->format('Y-m-d') }}</p>
        <p>Due Date: {{ $bill->due_date ? \Carbon\Carbon::parse($bill->due_date)->format('Y-m-d') : '-' }}</p>
    </div>

    <div class="bill-details">
        <h3>Supplier:</h3>
        <p><strong>{{ $bill->supplier->name ?? 'N/A' }}</strong></p>
        <p>{{ $bill->supplier->email ?? '' }}</p>
        <p>{{ $bill->supplier->phone ?? '' }}</p>
        <p>{{ $bill->supplier->address ?? '' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $bill->description ?: 'General supplies/services' }}</td>
                <td style="text-align: right;">{{ $company->currency }} {{ number_format($bill->total_amount, 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td style="text-align: right;">Amount Paid</td>
                <td style="text-align: right;">{{ $company->currency }} {{ number_format($bill->amount_paid ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td style="text-align: right;">Remaining</td>
                <td style="text-align: right;">{{ $company->currency }} {{ number_format(($bill->total_amount ?? 0) - ($bill->amount_paid ?? 0), 2) }}</td>
            </tr>
            <tr class="total">
                <td style="text-align: right;">Total</td>
                <td style="text-align: right;">{{ $company->currency }} {{ number_format($bill->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <p style="margin-top: 20px;">
        Status: <span class="status status-{{ $bill->status }}">{{ ucfirst($bill->status) }}</span>
    </p>
</body>
</html>

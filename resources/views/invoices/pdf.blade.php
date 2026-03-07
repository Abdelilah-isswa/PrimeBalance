<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .header { text-align: center; margin-bottom: 30px; }
        .company-info { margin-bottom: 20px; }
        .invoice-details { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f5f5f5; }
        .total { font-weight: bold; font-size: 16px; }
        .status { padding: 5px 10px; border-radius: 4px; display: inline-block; }
        .status-paid { background: #e8f5e9; color: #2e7d32; }
        .status-draft { background: #fff9c4; color: #f57f17; }
        .status-sent { background: #e3f2fd; color: #1565c0; }
        .status-cancelled { background: #ffebee; color: #c62828; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>Invoice #{{ $invoice->id }}</p>
    </div>
    
    <div class="company-info">
        <h3>{{ $company->name }}</h3>
        <p>{{ $company->address }}</p>
        <p>Date: {{ $invoice->created_at->format('Y-m-d') }}</p>
    </div>
    
    <div class="invoice-details">
        <h3>Bill To:</h3>
        <p><strong>{{ $invoice->client->name }}</strong></p>
        <p>{{ $invoice->client->email }}</p>
        <p>{{ $invoice->client->phone }}</p>
        <p>{{ $invoice->client->address }}</p>
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
                <td>Invoice #{{ $invoice->id }}</td>
                <td style="text-align: right;">{{ $company->currency }} {{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total">
                <td>Total</td>
                <td style="text-align: right;">{{ $company->currency }} {{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    
    <p style="margin-top: 20px;">
        Status: <span class="status status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
    </p>
</body>
</html>

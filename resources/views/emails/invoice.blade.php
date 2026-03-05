<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #333; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .invoice-details { background: white; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Invoice #{{ $invoice->id }}</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $invoice->client->name }},</p>
            
            <p>Please find below the details of your invoice from {{ $company->name }}.</p>
            
            <div class="invoice-details">
                <p><strong>Invoice Number:</strong> #{{ $invoice->id }}</p>
                <p><strong>Date:</strong> {{ $invoice->created_at->format('F d, Y') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($invoice->status) }}</p>
                <p><strong>Amount:</strong> {{ $company->currency }} {{ number_format($invoice->total_amount, 2) }}</p>
            </div>
            
            <p><strong>Company Details:</strong></p>
            <p>
                {{ $company->name }}<br>
                {{ $company->address }}<br>
            </p>
            
            <p>If you have any questions about this invoice, please contact us.</p>
            
            <p>Thank you for your business!</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $company->name }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

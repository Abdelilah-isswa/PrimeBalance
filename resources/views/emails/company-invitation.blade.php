<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #333; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .button { display: inline-block; padding: 12px 24px; background: #333; color: white; text-decoration: none; border-radius: 4px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Company Invitation</h1>
        </div>
        
        <div class="content">
            <p>Hello,</p>
            
            <p><strong>{{ $inviterName }}</strong> has invited you to join <strong>{{ $company->name }}</strong> as a <strong>{{ $role }}</strong>.</p>
            
            <p>If you already have an account, please log in and you will see the company in your companies list.</p>
            
            <p>If you don't have an account yet, please register first:</p>
            
            <a href="{{ config('app.url') }}/register" class="button">Register Now</a>
            
            <p>After registration or login, the company owner will need to add you to the company.</p>
            
            <p><strong>Company Details:</strong></p>
            <p>
                {{ $company->name }}<br>
                {{ $company->address }}<br>
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

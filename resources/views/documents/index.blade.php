@extends('layouts.app')

@section('title', 'Documents')

@section('content')
    <h1>Documents - {{ $company->name }}</h1>
    
    <form method="GET" action="/companies/{{ $company->id }}/documents" style="margin: 1rem 0;">
        <label><strong>View:</strong></label>
        <select name="type" onchange="this.form.submit()" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
            <option value="invoices" {{ $type === 'invoices' ? 'selected' : '' }}>Invoices</option>
            <option value="bills" {{ $type === 'bills' ? 'selected' : '' }}>Bills</option>
        </select>
    </form>
    
    <table style="width: 100%; margin: 2rem 0; border-collapse: collapse;">
        <thead>
            <tr style="background: #f5f5f5;">
                <th style="padding: 0.5rem; text-align: left; border: 1px solid #ddd;">ID</th>
                <th style="padding: 0.5rem; text-align: left; border: 1px solid #ddd;">{{ $type === 'invoices' ? 'Client' : 'Supplier' }}</th>
                <th style="padding: 0.5rem; text-align: right; border: 1px solid #ddd;">Amount</th>
                <th style="padding: 0.5rem; text-align: left; border: 1px solid #ddd;">Status</th>
                <th style="padding: 0.5rem; text-align: left; border: 1px solid #ddd;">Date</th>
                <th style="padding: 0.5rem; text-align: left; border: 1px solid #ddd;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documents as $doc)
            <tr>
                <td style="padding: 0.5rem; border: 1px solid #ddd;">#{{ $doc->id }}</td>
                <td style="padding: 0.5rem; border: 1px solid #ddd;">
                    {{ $type === 'invoices' ? $doc->client->name : $doc->supplier->name }}
                </td>
                <td style="padding: 0.5rem; text-align: right; border: 1px solid #ddd;">
                    {{ $company->currency }} {{ number_format($doc->total_amount, 2) }}
                </td>
                <td style="padding: 0.5rem; border: 1px solid #ddd;">
                    <span style="padding: 0.25rem 0.5rem; border-radius: 4px; background: 
                        {{ $doc->status === 'paid' ? '#e8f5e9' : ($doc->status === 'cancelled' ? '#ffebee' : '#fff9c4') }}; 
                        color: {{ $doc->status === 'paid' ? '#2e7d32' : ($doc->status === 'cancelled' ? '#c62828' : '#f57f17') }};">
                        {{ ucfirst($doc->status) }}
                    </span>
                </td>
                <td style="padding: 0.5rem; border: 1px solid #ddd;">{{ $doc->created_at->format('Y-m-d') }}</td>
                <td style="padding: 0.5rem; border: 1px solid #ddd;">
                    <a href="/companies/{{ $company->id }}/{{ $type }}/{{ $doc->id }}">
                        <button type="button" style="padding: 0.25rem 0.5rem;">View</button>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <a href="/companies/{{ $company->id }}">
        <button type="button">Back to Dashboard</button>
    </a>
@endsection

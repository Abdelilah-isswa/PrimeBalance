@extends('layouts.app')

@section('title', 'Supplier Balances')

@section('content')
    <h1>Supplier Balances - {{ $company->name }}</h1>
    
    <table style="width: 100%; margin: 2rem 0; border-collapse: collapse;">
        <thead>
            <tr style="background: #f5f5f5;">
                <th style="padding: 0.5rem; text-align: left; border: 1px solid #ddd;">Supplier Name</th>
                <th style="padding: 0.5rem; text-align: left; border: 1px solid #ddd;">Email</th>
                <th style="padding: 0.5rem; text-align: right; border: 1px solid #ddd;">Balance</th>
                <th style="padding: 0.5rem; text-align: left; border: 1px solid #ddd;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $supplier)
            <tr>
                <td style="padding: 0.5rem; border: 1px solid #ddd;">{{ $supplier->name }}</td>
                <td style="padding: 0.5rem; border: 1px solid #ddd;">{{ $supplier->email }}</td>
                <td style="padding: 0.5rem; text-align: right; border: 1px solid #ddd; color: {{ $supplier->balance < 0 ? '#c62828' : '#2e7d32' }}; font-weight: bold;">
                    {{ $company->currency }} {{ number_format($supplier->balance, 2) }}
                </td>
                <td style="padding: 0.5rem; border: 1px solid #ddd;">
                    @if($supplier->balance < 0)
                        <span style="color: #c62828;">We owe {{ $company->currency }} {{ number_format(abs($supplier->balance), 2) }}</span>
                    @elseif($supplier->balance > 0)
                        <span style="color: #2e7d32;">Overpaid</span>
                    @else
                        <span style="color: #666;">Settled</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <a href="/companies/{{ $company->id }}">
        <button type="button">Back to Dashboard</button>
    </a>
@endsection

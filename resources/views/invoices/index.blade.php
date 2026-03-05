@extends('layouts.app')

@section('title', 'Invoices History')

@section('content')
    <h1>Invoices History - {{ $company->name }}</h1>
    
    @if($invoices->isEmpty())
        <p>No invoices yet.</p>
    @else
        <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
            <thead>
                <tr style="background: #f5f5f5;">
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">ID</th>
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Client</th>
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Total Amount</th>
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Status</th>
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Date</th>
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                    <tr style="cursor: pointer;" onclick="window.location='/companies/{{ $company->id }}/invoices/{{ $invoice->id }}'">
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">{{ $invoice->id }}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">{{ $invoice->client->name }}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">{{ $company->currency }} {{ $invoice->total_amount }}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">{{ ucfirst($invoice->status) }}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">{{ $invoice->created_at->format('Y-m-d') }}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;" onclick="event.stopPropagation();">
                            @if($invoice->status !== 'paid' && $company->pivot->role === 'owner')
                                <a href="/companies/{{ $company->id }}/invoices/{{ $invoice->id }}/receive">
                                    <button type="button" style="padding: 0.25rem 0.5rem; font-size: 0.9rem;">Receive Payment</button>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
    <a href="/companies/{{ $company->id }}" style="display: inline-block; margin-top: 1rem;">
        <button type="button">Back to Company</button>
    </a>
@endsection

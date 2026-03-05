@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
    <h1>Transactions - {{ $company->name }}</h1>
    
    @if($company->pivot->role === 'owner')
        <a href="/companies/{{ $company->id }}/transactions/create" style="display: inline-block; margin-bottom: 1rem;">
            <button type="button">Add Transaction</button>
        </a>
    @endif
    
    @if($transactions->isEmpty())
        <p>No transactions yet.</p>
    @else
        <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
            <thead>
                <tr style="background: #f5f5f5;">
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Date</th>
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Type</th>
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Description</th>
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Account</th>
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Category</th>
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Amount</th>
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Related</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">{{ $transaction->date->format('Y-m-d') }}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">
                            <span style="color: {{ $transaction->type === 'income' ? 'green' : 'red' }};">
                                {{ ucfirst($transaction->type) }}
                            </span>
                        </td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">{{ $transaction->description }}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">{{ $transaction->account->name }}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">{{ $transaction->category?->name ?? '-' }}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">
                            <span style="color: {{ $transaction->type === 'income' ? 'green' : 'red' }};">
                                {{ $transaction->type === 'income' ? '+' : '-' }}{{ $company->currency }} {{ $transaction->amount }}
                            </span>
                        </td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">
                            @if($transaction->invoice)
                                Invoice #{{ $transaction->invoice->id }} ({{ $transaction->invoice->client->name }})
                            @elseif($transaction->bill)
                                Bill #{{ $transaction->bill->id }} ({{ $transaction->bill->supplier->name }})
                            @else
                                -
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

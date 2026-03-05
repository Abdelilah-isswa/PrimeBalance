@extends('layouts.app')

@section('title', 'Accounts')

@section('content')
    <h1>Accounts - {{ $company->name }}</h1>
    
    <a href="/companies/{{ $company->id }}/accounts/create">
        <button type="button">Create Account</button>
    </a>
    
    <table style="width: 100%; margin: 2rem 0; border-collapse: collapse;">
        <thead>
            <tr style="background: #f5f5f5;">
                <th style="padding: 0.5rem; text-align: left; border: 1px solid #ddd;">Name</th>
                <th style="padding: 0.5rem; text-align: left; border: 1px solid #ddd;">Balance</th>
                <th style="padding: 0.5rem; text-align: left; border: 1px solid #ddd;">Status</th>
                <th style="padding: 0.5rem; text-align: left; border: 1px solid #ddd;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $account)
            <tr>
                <td style="padding: 0.5rem; border: 1px solid #ddd;">{{ $account->name }}</td>
                <td style="padding: 0.5rem; border: 1px solid #ddd;">{{ $company->currency }} {{ number_format($account->balance, 2) }}</td>
                <td style="padding: 0.5rem; border: 1px solid #ddd;">{{ $account->is_active ? 'Active' : 'Inactive' }}</td>
                <td style="padding: 0.5rem; border: 1px solid #ddd;">
                    <a href="/companies/{{ $company->id }}/accounts/{{ $account->id }}/edit">
                        <button type="button" style="padding: 0.25rem 0.5rem;">Edit</button>
                    </a>
                    <form method="POST" action="/companies/{{ $company->id }}/accounts/{{ $account->id }}" style="display: inline;" onsubmit="return confirm('{{ $account->transactions_count > 0 ? 'This account has transactions and will be archived.' : 'Delete this account?' }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: #c62828; padding: 0.25rem 0.5rem;">{{ $account->transactions_count > 0 ? 'Archive' : 'Delete' }}</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <a href="/companies/{{ $company->id }}">
        <button type="button">Back to Company</button>
    </a>
@endsection

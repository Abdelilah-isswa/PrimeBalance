@extends('layouts.app')

@section('title', $company->name)

@section('content')
    <h1>{{ $company->name }} @if($company->end_date)<span style="color: red; font-size: 1rem;">(Deactivated)</span>@endif</h1>
    
    <div style="margin: 2rem 0;">
        <p><strong>Address:</strong> {{ $company->address }}</p>
        <p><strong>Currency:</strong> {{ $company->currency }}</p>
        <p><strong>Start Date:</strong> {{ $company->start_date }}</p>
        @if($company->end_date)
            <p><strong>End Date:</strong> {{ $company->end_date }}</p>
        @endif
        <p><strong>Your Role:</strong> {{ $company->pivot->role }}</p>
        @if(!$company->end_date)
            @if($company->pivot->role === 'owner')
                <a href="/companies/{{ $company->id }}/edit">
                    <button type="button" style="margin-top: 0.5rem;">Manage Company</button>
                </a>
                <a href="/companies/{{ $company->id }}/edit#invite">
                    <button type="button" style="margin-top: 0.5rem; margin-left: 0.5rem;">Invite Users</button>
                </a>
            @endif
            <a href="/companies/{{ $company->id }}/invoices">
                <button type="button" style="margin-top: 0.5rem;">View Invoices History</button>
            </a>
            <a href="/companies/{{ $company->id }}/bills">
                <button type="button" style="margin-top: 0.5rem; margin-left: 0.5rem;">View Bills History</button>
            </a>
            <a href="/companies/{{ $company->id }}/transactions">
                <button type="button" style="margin-top: 0.5rem; margin-left: 0.5rem;">View Transactions</button>
            </a>
        @else
            <p style="margin-top: 1rem; color: #666;"><em>This company is deactivated. No actions can be performed.</em></p>
        @endif
    </div>

    <h2>Dashboard</h2>
    <form method="GET" action="/companies/{{ $company->id }}" style="margin-bottom: 1rem; display: flex; gap: 0.5rem; align-items: end;">
        <div>
            <label><strong>From:</strong></label>
            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" max="{{ date('Y-m-d') }}" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;" onchange="updateEndDateMin()">
        </div>
        <div>
            <label><strong>To:</strong></label>
            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" max="{{ date('Y-m-d') }}" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        <button type="submit" style="padding: 0.5rem 1rem;">Filter</button>
        @if($startDate || $endDate)
            <a href="/companies/{{ $company->id }}">
                <button type="button" style="padding: 0.5rem 1rem; background: #666;">Clear</button>
            </a>
        @endif
    </form>
    <script>
        function updateEndDateMin() {
            const startDate = document.getElementById('start_date').value;
            const endDateInput = document.getElementById('end_date');
            if (startDate) {
                endDateInput.min = startDate;
                if (endDateInput.value && endDateInput.value < startDate) {
                    endDateInput.value = startDate;
                }
            }
        }
        // Set initial min on page load
        updateEndDateMin();
        
        // Validate form before submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if ((startDate && !endDate) || (!startDate && endDate)) {
                e.preventDefault();
                alert('Please select both start and end dates');
                return false;
            }
            
            if (startDate && endDate && startDate > endDate) {
                e.preventDefault();
                alert('End date must be after start date');
                return false;
            }
        });
    </script>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin: 1rem 0;">
        <div style="padding: 1rem; background: #e8f5e9; border-radius: 4px;">
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem; color: #2e7d32;">Total Income</h3>
            <p style="margin: 0; font-size: 1.5rem; font-weight: bold; color: #2e7d32;">{{ $company->currency }} {{ number_format($totalIncome, 2) }}</p>
        </div>
        <div style="padding: 1rem; background: #ffebee; border-radius: 4px;">
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem; color: #c62828;">Total Expense</h3>
            <p style="margin: 0; font-size: 1.5rem; font-weight: bold; color: #c62828;">{{ $company->currency }} {{ number_format($totalExpense, 2) }}</p>
        </div>
        <div style="padding: 1rem; background: {{ $netProfit >= 0 ? '#e3f2fd' : '#fff3e0' }}; border-radius: 4px;">
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem; color: {{ $netProfit >= 0 ? '#1565c0' : '#e65100' }};">Net Profit</h3>
            <p style="margin: 0; font-size: 1.5rem; font-weight: bold; color: {{ $netProfit >= 0 ? '#1565c0' : '#e65100' }};">{{ $company->currency }} {{ number_format($netProfit, 2) }}</p>
        </div>
        <div style="padding: 1rem; background: #f3e5f5; border-radius: 4px;">
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem; color: #6a1b9a;">Bank Balance</h3>
            <p style="margin: 0; font-size: 1.5rem; font-weight: bold; color: #6a1b9a;">{{ $company->currency }} {{ number_format($bankBalance, 2) }}</p>
        </div>
        <div style="padding: 1rem; background: #fff9c4; border-radius: 4px;">
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem; color: #f57f17;">Unpaid Invoices</h3>
            <p style="margin: 0; font-size: 1.5rem; font-weight: bold; color: #f57f17;">{{ $unpaidInvoices }}</p>
            <a href="/companies/{{ $company->id }}/clients/balances" style="display: inline-block; margin-top: 0.5rem; color: #f57f17; text-decoration: underline; font-size: 0.9rem;">View Client Balances</a>
        </div>
        <div style="padding: 1rem; background: #fce4ec; border-radius: 4px;">
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem; color: #c2185b;">Unpaid Bills</h3>
            <p style="margin: 0; font-size: 1.5rem; font-weight: bold; color: #c2185b;">{{ $unpaidBills }}</p>
            <a href="/companies/{{ $company->id }}/suppliers/balances" style="display: inline-block; margin-top: 0.5rem; color: #c2185b; text-decoration: underline; font-size: 0.9rem;">View Supplier Balances</a>
        </div>
    </div>

    @if(!$company->end_date)

    <h2>Categories</h2>
    <a href="/companies/{{ $company->id }}/categories">
        <button type="button" style="margin-bottom: 1rem;">Manage Categories</button>
    </a>

    @if($company->categories->isEmpty())
        <p>No categories yet.</p>
    @else
        <ul>
            @foreach($company->categories as $category)
                <li>{{ $category->name }}</li>
            @endforeach
        </ul>
    @endif

    <h2>Clients</h2>
    @if($company->pivot->role === 'owner')
        <a href="/companies/{{ $company->id }}/clients/create" style="display: inline-block; margin-bottom: 1rem;">
            <button type="button">Add Client</button>
        </a>
    @endif

    @if($company->clients->isEmpty())
        <p>No clients yet.</p>
    @else
        <ul>
            @foreach($company->clients as $client)
                <li>
                    {{ $client->name }} - {{ $client->email }} - {{ $client->phone }}
                    @if($company->pivot->role === 'owner')
                        <a href="/companies/{{ $company->id }}/clients/{{ $client->id }}/edit">
                            <button type="button" style="margin-left: 0.5rem; padding: 0.25rem 0.5rem; font-size: 0.9rem;">Edit</button>
                        </a>
                        <a href="/companies/{{ $company->id }}/clients/{{ $client->id }}/invoices/create">
                            <button type="button" style="margin-left: 0.5rem; padding: 0.25rem 0.5rem; font-size: 0.9rem;">Add Invoice</button>
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    <h2>Suppliers</h2>
    @if($company->pivot->role === 'owner')
        <a href="/companies/{{ $company->id }}/suppliers/create" style="display: inline-block; margin-bottom: 1rem;">
            <button type="button">Add Supplier</button>
        </a>
    @endif

    @if($company->suppliers->isEmpty())
        <p>No suppliers yet.</p>
    @else
        <ul>
            @foreach($company->suppliers as $supplier)
                <li>
                    {{ $supplier->name }} - {{ $supplier->email }} - {{ $supplier->phone }}
                    @if($company->pivot->role === 'owner')
                        <a href="/companies/{{ $company->id }}/suppliers/{{ $supplier->id }}/edit">
                            <button type="button" style="margin-left: 0.5rem; padding: 0.25rem 0.5rem; font-size: 0.9rem;">Edit</button>
                        </a>
                        <a href="/companies/{{ $company->id }}/suppliers/{{ $supplier->id }}/bills/create">
                            <button type="button" style="margin-left: 0.5rem; padding: 0.25rem 0.5rem; font-size: 0.9rem;">Add Bill</button>
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    <h2>Accounts</h2>
    @if($company->pivot->role === 'owner')
        <a href="/companies/{{ $company->id }}/accounts" style="display: inline-block; margin-bottom: 1rem;">
            <button type="button">Manage Accounts</button>
        </a>
    @endif

    @if($company->accounts->isEmpty())
        <p>No accounts yet.</p>
    @else
        <ul>
            @foreach($company->accounts as $account)
                <li>{{ $account->name }} - Balance: {{ $account->balance }} - {{ $account->is_active ? 'Active' : 'Inactive' }}</li>
            @endforeach
        </ul>
    @endif
    @endif
@endsection

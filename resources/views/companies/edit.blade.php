@extends('layouts.app')

@section('title', 'Manage Company')

@section('content')
    <h1>Manage Company</h1>
    
    <div id="view-mode" style="margin: 2rem 0;">
        <p><strong>Name:</strong> <span id="display-name">{{ $company->name }}</span></p>
        <p><strong>Address:</strong> <span id="display-address">{{ $company->address }}</span></p>
        <p><strong>Currency:</strong> <span id="display-currency">{{ $company->currency }}</span></p>
        <p><strong>Start Date:</strong> {{ $company->start_date }}</p>
        @if($company->end_date)
            <p><strong>End Date:</strong> {{ $company->end_date }} <span style="color: red;">(Deactivated)</span></p>
        @endif
        <button type="button" onclick="showEditMode()" style="margin-top: 1rem;">Update</button>
        @if(!$company->end_date)
            <form method="POST" action="/companies/{{ $company->id }}/deactivate" style="display: inline; margin-left: 0.5rem;" onsubmit="return confirm('Are you sure you want to deactivate this company? This will set the end date to today.')">
                @csrf
                <button type="submit" style="background: #c62828;">Deactivate Company</button>
            </form>
        @endif
    </div>

    @if(!$company->end_date)
    <h2>Invite User</h2>
    <form method="POST" action="/companies/{{ $company->id }}/invite" style="margin: 1rem 0;">
        @csrf
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Role:</label>
            <select name="role" required>
                <option value="owner">Owner</option>
                <option value="accountant">Accountant</option>
                <option value="standard_user">Standard User</option>
                <option value="viewer">Viewer</option>
            </select>
        </div>
        <button type="submit">Send Invitation</button>
    </form>
    @endif

    <form id="edit-mode" method="POST" action="/companies/{{ $company->id }}" style="display: none;">
        @csrf
        @method('PUT')
        <div>
            <label>Name:</label>
            <input type="text" name="name" value="{{ $company->name }}" required>
            @error('name')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Address:</label>
            <input type="text" name="address" value="{{ $company->address }}" required>
            @error('address')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Currency:</label>
            <select name="currency" required>
                <option value="USD" {{ $company->currency === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                <option value="EUR" {{ $company->currency === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                <option value="GBP" {{ $company->currency === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                <option value="MAD" {{ $company->currency === 'MAD' ? 'selected' : '' }}>MAD - Moroccan Dirham</option>
                <option value="JPY" {{ $company->currency === 'JPY' ? 'selected' : '' }}>JPY - Japanese Yen</option>
                <option value="CNY" {{ $company->currency === 'CNY' ? 'selected' : '' }}>CNY - Chinese Yuan</option>
                <option value="CAD" {{ $company->currency === 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                <option value="AUD" {{ $company->currency === 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
            </select>
            @error('currency')<span>{{ $message }}</span>@enderror
        </div>
        <button type="submit" style="margin-right: 0.5rem;">Save</button>
        <button type="button" onclick="showViewMode()">Cancel</button>
    </form>
    
    <a href="/companies/{{ $company->id }}" style="display: inline-block; margin-top: 1rem;">
        <button type="button">Back to Company</button>
    </a>

    <script>
        function showEditMode() {
            document.getElementById('view-mode').style.display = 'none';
            document.getElementById('edit-mode').style.display = 'block';
        }

        function showViewMode() {
            document.getElementById('view-mode').style.display = 'block';
            document.getElementById('edit-mode').style.display = 'none';
        }
    </script>
@endsection

@extends('layouts.app')

@section('title', 'My Companies')

@section('content')
    <h1>My Companies</h1>
    
    @if($companies->isEmpty())
        <p>You don't have any companies yet. <a href="/companies/create">Create one</a></p>
    @else
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5f5f5;">
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Name</th>
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Address</th>
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Currency</th>
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Start Date</th>
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #ddd;">Role</th>
                </tr>
            </thead>
            <tbody>
                @foreach($companies as $company)
                    <tr style="cursor: pointer;" onclick="window.location='/companies/{{ $company->id }}'">
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">{{ $company->name }}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">{{ $company->address }}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">{{ $company->currency }}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">{{ $company->start_date }}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #ddd;">{{ $company->pivot->role }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection

@extends('layouts.app')

@section('title', 'Company Invitation')

@section('content')
    <div style="max-width: 600px; margin: 3rem auto; text-align: center;">
        <h1>You're Invited!</h1>
        
        <div style="margin: 2rem 0; padding: 2rem; background: #f5f5f5; border-radius: 8px;">
            <p style="font-size: 1.2rem; margin-bottom: 1rem;">
                <strong>{{ $invitation->inviter->name }}</strong> has invited you to join
            </p>
            <h2 style="color: #333; margin: 1rem 0;">{{ $invitation->company->name }}</h2>
            <p style="font-size: 1.1rem; color: #666;">
                as a <strong>{{ ucfirst(str_replace('_', ' ', $invitation->role)) }}</strong>
            </p>
            
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #ddd;">
                <p><strong>Company Details:</strong></p>
                <p>{{ $invitation->company->address }}</p>
                <p style="color: #666; font-size: 0.9rem; margin-top: 1rem;">
                    Invitation expires: {{ $invitation->expires_at->format('F d, Y') }}
                </p>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: center;">
            <form method="POST" action="/invitations/{{ $invitation->token }}/accept">
                @csrf
                <button type="submit" style="padding: 1rem 2rem; font-size: 1.1rem; background: #2e7d32;">
                    Accept Invitation
                </button>
            </form>
            
            <form method="POST" action="/invitations/{{ $invitation->token }}/decline" onsubmit="return confirm('Are you sure you want to decline this invitation?');">
                @csrf
                <button type="submit" style="padding: 1rem 2rem; font-size: 1.1rem; background: #c62828;">
                    Decline
                </button>
            </form>
        </div>
        
        <p style="margin-top: 2rem; color: #666; font-size: 0.9rem;">
            This invitation was sent to: <strong>{{ $invitation->email }}</strong>
        </p>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Account Rejected')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%); display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div style="max-width: 500px; background: white; border-radius: 20px; padding: 40px; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.1);">
        <div style="font-size: 80px; margin-bottom: 20px;">❌</div>
        <h1 style="color: #dc2626; margin-bottom: 15px;">Account Rejected</h1>
        <p style="color: #6e7f72; margin-bottom: 20px; line-height: 1.6;">
            We're sorry, but your account application has been rejected.
        </p>
        @if(Auth::user()->rejection_reason)
        <div style="background: #fef2f2; padding: 15px; border-radius: 12px; margin: 20px 0; text-align: left;">
            <p style="font-size: 13px; color: #dc2626; margin-bottom: 5px;"><strong>Reason:</strong></p>
            <p style="font-size: 13px; color: #6e7f72;">{{ Auth::user()->rejection_reason }}</p>
        </div>
        @endif
        <p style="font-size: 13px; color: #6e7f72; margin-bottom: 20px;">
            For inquiries, please contact the UCC-ERS administrator.
        </p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background: #dc2626; color: white; border: none; padding: 12px 30px; border-radius: 50px; cursor: pointer; font-weight: 600;">
                Logout
            </button>
        </form>
    </div>
</div>
@endsection
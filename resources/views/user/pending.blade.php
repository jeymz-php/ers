@extends('layouts.app')

@section('title', 'Account Pending')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #f0faf3 0%, #ffffff 100%); display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div style="max-width: 500px; background: white; border-radius: 20px; padding: 40px; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.1);">
        <div style="font-size: 80px; margin-bottom: 20px;">⏳</div>
        <h1 style="color: #1a7a3e; margin-bottom: 15px;">Account Pending Approval</h1>
        <p style="color: #6e7f72; margin-bottom: 20px; line-height: 1.6;">
            Thank you for registering! Your account is currently awaiting admin approval.
            You will receive an email notification once your account has been approved.
        </p>
        <div style="background: #f7faf8; padding: 15px; border-radius: 12px; margin: 20px 0;">
            <p style="font-size: 13px; color: #1a7a3e;">
                📧 <strong>{{ Auth::user()->email }}</strong>
            </p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background: #2db84f; color: white; border: none; padding: 12px 30px; border-radius: 50px; cursor: pointer; font-weight: 600;">
                Logout
            </button>
        </form>
    </div>
</div>
@endsection
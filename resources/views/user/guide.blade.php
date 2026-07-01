@extends('layouts.app')

@section('title', 'User Guide')

@section('content')
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background: #f0faf3; font-family: 'Arial', sans-serif; overflow-x: hidden; }

    .user-container { display: flex; min-height: 100vh; overflow-x: hidden; }

    .user-main {
        flex: 1;
        margin-left: 280px;
        background: #f0faf3;
        min-height: 100vh;
        transition: margin-left 0.3s ease;
    }
    .content-area { padding: 25px; }

    .guide-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .guide-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e8eee9;
        flex-wrap: wrap;
        gap: 10px;
    }
    .guide-title { font-size: 18px; font-weight: 700; color: #1a7a3e; }
    .btn-download {
        background: #1a7a3e;
        color: white;
        padding: 8px 18px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
    }
    .btn-download:hover { background: #155e30; color: white; text-decoration: none; }
    .pdf-viewer { width: 100%; height: 85vh; border: none; border-radius: 8px; }

    @media (max-width: 768px) {
        .user-main { margin-left: 0; }
        .pdf-viewer { height: 70vh; }
    }
</style>

<div class="user-container">
    @include('partials.user-sidebar')

    <main class="user-main">
        @include('partials.user-topbar')

        <div class="content-area">
            <div class="guide-card">
                <div class="guide-header">
                    <div class="guide-title">📖 UCC-ERS User Guide</div>
                    <a href="{{ asset('guides/USER GUIDE OF UCC ERS.pdf') }}" download class="btn-download">⬇ Download PDF</a>
                </div>

                <iframe
                    src="{{ asset('guides/USER GUIDE OF UCC ERS.pdf') }}"
                    class="pdf-viewer"
                    title="UCC-ERS User Guide">
                    <p>Your browser does not support PDF preview.
                        <a href="{{ asset('guides/USER GUIDE OF UCC ERS.pdf') }}">Click here to download the guide.</a>
                    </p>
                </iframe>
            </div>
        </div>
    </main>
</div>
@endsection
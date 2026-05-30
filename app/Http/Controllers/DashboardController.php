<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /* ──────────────────────────────────────────────────
     | Show the main dashboard
     ─────────────────────────────────────────────────── */
    public function index(Request $request)
    {
        $user = Auth::user();

        return view('dashboard', compact('user'));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        if (method_exists($user, 'role') || method_exists($user, 'unit')) {
            $user->load(['role','unit']);
        }

        $role = optional($user->role)->role_name ?? 'Role Tidak Dikenali';
        $unit = optional($user->unit)->unit_name ?? null;
        return view('dashboard.index', compact('user','role','unit'));
    }
}

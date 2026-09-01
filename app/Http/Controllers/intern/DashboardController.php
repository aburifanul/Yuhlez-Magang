<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('intern.dashboard', compact('user'));
    }
}
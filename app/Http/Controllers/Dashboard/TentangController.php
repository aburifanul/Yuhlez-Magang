<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class TentangController extends Controller
{
    public function index()
    {
        return view('dashboard.tentang');
    }
}
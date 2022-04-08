<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check_permission');
    }

    public function dashboard()
    {
        $total_users = User::count();
        return view('dashboard.dashboard', compact('total_users'));
    }
}

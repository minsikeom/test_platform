<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {}

    public function index(Request $request)
    {
        // echo $request->getLanguages()[0];
        return view('admin/dashboard');
    }
}

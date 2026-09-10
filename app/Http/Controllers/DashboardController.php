<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'employeeCount' => Employee::count(),
            'departmentCount' => Department::count(),
            'recentEmployees' => Employee::with('department')->latest()->take(5)->get(),
            'departmentStats' => Department::withCount('employees')->orderBy('name')->get(),
        ]);
    }
}

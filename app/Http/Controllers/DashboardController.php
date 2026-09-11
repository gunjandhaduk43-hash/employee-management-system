<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();
        $employeeCount = Employee::count();
        $present = Attendance::whereDate('attendance_date', $today)->where('status', 'Present')->count();
        $absent = Attendance::whereDate('attendance_date', $today)->where('status', 'Absent')->count();
        $halfDay = Attendance::whereDate('attendance_date', $today)->where('status', 'Half Day')->count();
        $leave = Attendance::whereDate('attendance_date', $today)->where('status', 'Leave')->count();
        $totalMarked = $present + $absent + $halfDay + $leave;
        $pending = max(0, $employeeCount - $totalMarked);

        $todayAttendance = [
            'present' => $present,
            'absent' => $absent,
            'half_day' => $halfDay,
            'leave' => $leave,
            'total' => $totalMarked,
            'pending' => $pending,
            'employee_count' => $employeeCount,
        ];

        return view('dashboard', [
            'employeeCount' => $employeeCount,
            'departmentCount' => Department::count(),
            'recentEmployees' => Employee::with('department')->latest()->take(5)->get(),
            'departmentStats' => Department::withCount('employees')->orderBy('name')->get(),
            'todayAttendance' => $todayAttendance,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceRequest;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        // 1. Determine selected date (defaults to today)
        $dateInput = $request->input('date');
        if ($dateInput) {
            try {
                $selectedDate = Carbon::parse($dateInput)->toDateString();
            } catch (\Exception $e) {
                $selectedDate = now()->toDateString();
            }
        } else {
            $selectedDate = now()->toDateString();
        }

        // 2. Summary KPI Metrics for the selected date
        $totalEmployees = Employee::count();
        $presentCount = Attendance::whereDate('attendance_date', $selectedDate)->where('status', 'Present')->count();
        $absentCount = Attendance::whereDate('attendance_date', $selectedDate)->where('status', 'Absent')->count();
        $halfDayCount = Attendance::whereDate('attendance_date', $selectedDate)->where('status', 'Half Day')->count();
        $leaveCount = Attendance::whereDate('attendance_date', $selectedDate)->where('status', 'Leave')->count();
        $pendingCount = max(0, $totalEmployees - ($presentCount + $absentCount + $halfDayCount + $leaveCount));

        // 3. Base Query: ALL Employees with their attendance on selected date
        $query = Employee::with([
            'department',
            'attendances' => function ($q) use ($selectedDate) {
                $q->whereDate('attendance_date', $selectedDate);
            },
        ]);

        // 4. Department filter
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->integer('department_id'));
        }

        // 5. Search filter (by employee ID, name, email)
        if ($request->filled('search')) {
            $search = trim((string) $request->string('search'));
            $query->where(function ($builder) use ($search) {
                $builder->where('employee_id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 6. Status filter (including Pending)
        if ($request->filled('status')) {
            $status = (string) $request->string('status');
            if ($status === 'Pending') {
                $query->whereDoesntHave('attendances', function ($q) use ($selectedDate) {
                    $q->whereDate('attendance_date', $selectedDate);
                });
            } elseif (in_array($status, ['Present', 'Absent', 'Half Day', 'Leave'])) {
                $query->whereHas('attendances', function ($q) use ($selectedDate, $status) {
                    $q->whereDate('attendance_date', $selectedDate)->where('status', $status);
                });
            }
        }

        // 7. Paginated employees list
        $employees = $query->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        $departments = Department::orderBy('name')->get();
        $statuses = ['Present', 'Absent', 'Half Day', 'Leave', 'Pending'];

        return view('attendances.index', compact(
            'employees',
            'departments',
            'statuses',
            'selectedDate',
            'totalEmployees',
            'presentCount',
            'absentCount',
            'halfDayCount',
            'leaveCount',
            'pendingCount'
        ));
    }

    public function create(Request $request): View
    {
        $employees = Employee::with('department')->orderBy('name')->get();
        $selectedEmployeeId = $request->integer('employee_id') ?: null;
        $dateInput = $request->input('date');

        if ($dateInput) {
            try {
                $selectedDate = Carbon::parse($dateInput)->toDateString();
            } catch (\Exception $e) {
                $selectedDate = now()->toDateString();
            }
        } else {
            $selectedDate = now()->toDateString();
        }

        $statuses = ['Present', 'Absent', 'Half Day', 'Leave'];

        return view('attendances.create', [
            'attendance' => new Attendance([
                'attendance_date' => $selectedDate,
                'status' => 'Present',
            ]),
            'employees' => $employees,
            'selectedEmployeeId' => $selectedEmployeeId,
            'selectedDate' => $selectedDate,
            'statuses' => $statuses,
        ]);
    }

    public function store(AttendanceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (in_array($data['status'], ['Absent', 'Leave'])) {
            $data['check_in'] = null;
            $data['check_out'] = null;
        }

        Attendance::create($data);

        return redirect()
            ->route('attendances.index', ['date' => $data['attendance_date']])
            ->with('success', 'Attendance marked successfully.');
    }

    public function edit(Attendance $attendance): View
    {
        $attendance->load('employee.department');
        $employees = Employee::with('department')->orderBy('name')->get();
        $statuses = ['Present', 'Absent', 'Half Day', 'Leave'];

        return view('attendances.edit', compact('attendance', 'employees', 'statuses'));
    }

    public function update(AttendanceRequest $request, Attendance $attendance): RedirectResponse
    {
        $data = $request->validated();

        if (in_array($data['status'], ['Absent', 'Leave'])) {
            $data['check_in'] = null;
            $data['check_out'] = null;
        }

        $attendance->update($data);

        return redirect()
            ->route('attendances.index', ['date' => $data['attendance_date']])
            ->with('success', 'Attendance record updated successfully.');
    }

    public function destroy(Attendance $attendance): RedirectResponse
    {
        $date = $attendance->attendance_date instanceof Carbon
            ? $attendance->attendance_date->toDateString()
            : substr((string) $attendance->attendance_date, 0, 10);

        $attendance->delete();

        return redirect()
            ->route('attendances.index', ['date' => $date])
            ->with('success', 'Attendance record deleted successfully.');
    }

    public function employeeHistory(Request $request, Employee $employee): View
    {
        $employee->load('department');

        $monthInput = (string) $request->input('month', now()->format('Y-m'));

        try {
            $startDate = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $currentMonth = $startDate->format('Y-m');
        } catch (\Exception $e) {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
            $currentMonth = now()->format('Y-m');
        }

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('attendance_date', 'asc')
            ->get();

        $presentCount = $attendances->where('status', 'Present')->count();
        $absentCount = $attendances->where('status', 'Absent')->count();
        $halfDayCount = $attendances->where('status', 'Half Day')->count();
        $leaveCount = $attendances->where('status', 'Leave')->count();

        // Formula: (Present + Half Day * 0.5) / (Present + Half Day + Absent) * 100
        $applicableDays = $presentCount + $halfDayCount + $absentCount;
        $percentage = $applicableDays > 0
            ? round((($presentCount + ($halfDayCount * 0.5)) / $applicableDays) * 100, 2)
            : 0;

        return view('attendances.employee-history', compact(
            'employee',
            'attendances',
            'currentMonth',
            'presentCount',
            'absentCount',
            'halfDayCount',
            'leaveCount',
            'applicableDays',
            'percentage'
        ));
    }
}

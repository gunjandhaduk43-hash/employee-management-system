@extends('layouts.ems')

@section('title', 'Dashboard')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="h3 fw-bold text-dark mb-1">System Overview</h2>
        <p class="text-muted mb-0">Welcome back, <strong>{{ Auth::user()->name }}</strong>. Here is your organization summary.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <a href="{{ route('employees.create') }}" class="btn btn-primary me-2">
            <i class="bi bi-person-plus-fill me-1"></i> Add Employee
        </a>
        <a href="{{ route('departments.create') }}" class="btn btn-outline-primary me-2">
            <i class="bi bi-building-add me-1"></i> Add Department
        </a>
        <a href="{{ route('attendances.create') }}" class="btn btn-outline-success">
            <i class="bi bi-calendar-plus me-1"></i> Mark Attendance
        </a>
    </div>
</div>

<!-- Stat KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card card-custom d-flex align-items-center">
            <div class="stat-icon bg-primary-subtle text-primary me-3">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase">Total Employees</div>
                <div class="h2 fw-bold text-dark mb-0">{{ $employeeCount }}</div>
            </div>
            <a href="{{ route('employees.index') }}" class="stretched-link" aria-label="View Employees"></a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card card-custom d-flex align-items-center">
            <div class="stat-icon bg-success-subtle text-success me-3">
                <i class="bi bi-building"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase">Departments</div>
                <div class="h2 fw-bold text-dark mb-0">{{ $departmentCount }}</div>
            </div>
            <a href="{{ route('departments.index') }}" class="stretched-link" aria-label="View Departments"></a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card card-custom d-flex align-items-center">
            <div class="stat-icon bg-info-subtle text-info me-3">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase">System Status</div>
                <div class="h4 fw-bold text-dark mb-0 d-flex align-items-center">
                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 fs-6">
                        <i class="bi bi-check-circle-fill me-1"></i> Operational
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Today's Attendance Overview Card -->
<div class="card card-custom mb-4 border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-success-subtle text-success p-2 me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="bi bi-calendar-check fs-5"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark">Today's Attendance Overview</h5>
                <small class="text-muted">{{ \Carbon\Carbon::today()->format('l, F j, Y') }}</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('attendances.index') }}" class="btn btn-sm btn-success fw-semibold">
                <i class="bi bi-calendar2-check me-1"></i> Mark Attendance
            </a>
            <a href="{{ route('attendances.index') }}" class="btn btn-sm btn-outline-primary">
                View Full Sheet <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    <div class="card-body p-4">
        <div class="row g-3 text-center">
            <div class="col-4 col-md-2">
                <div class="p-3 rounded-3 border" style="background-color: #f8fafc;">
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">
                        <i class="bi bi-people-fill me-1 text-primary"></i> Total
                    </div>
                    <div class="h3 fw-bold text-dark mb-0">{{ $employeeCount }}</div>
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="p-3 rounded-3 bg-success-subtle border border-success-subtle">
                    <div class="text-success small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">
                        <i class="bi bi-check-circle-fill me-1"></i> Present
                    </div>
                    <div class="h3 fw-bold text-success mb-0">{{ $todayAttendance['present'] }}</div>
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="p-3 rounded-3 bg-danger-subtle border border-danger-subtle">
                    <div class="text-danger small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">
                        <i class="bi bi-x-circle-fill me-1"></i> Absent
                    </div>
                    <div class="h3 fw-bold text-danger mb-0">{{ $todayAttendance['absent'] }}</div>
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="p-3 rounded-3 bg-warning-subtle border border-warning-subtle">
                    <div class="text-warning-emphasis small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">
                        <i class="bi bi-clock-fill me-1"></i> Half Day
                    </div>
                    <div class="h3 fw-bold text-warning-emphasis mb-0">{{ $todayAttendance['half_day'] }}</div>
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="p-3 rounded-3 bg-info-subtle border border-info-subtle">
                    <div class="text-info-emphasis small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">
                        <i class="bi bi-calendar2-range-fill me-1"></i> Leave
                    </div>
                    <div class="h3 fw-bold text-info-emphasis mb-0">{{ $todayAttendance['leave'] }}</div>
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="p-3 rounded-3 border" style="background-color: #ffedd5; border-color: #fdba74 !important;">
                    <div class="text-uppercase mb-1 fw-semibold" style="color: #c2410c; font-size: 0.72rem;">
                        <i class="bi bi-hourglass-split me-1"></i> Pending
                    </div>
                    <div class="h3 fw-bold mb-0" style="color: #c2410c;">{{ $todayAttendance['pending'] }}</div>
                </div>
            </div>
        </div>

        <div class="mt-3 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center text-muted small">
            <div>
                <i class="bi bi-info-circle me-1 text-primary"></i>
                Total recorded: <strong>{{ $todayAttendance['total'] }}</strong> of <strong>{{ $employeeCount }}</strong> {{ Str::plural('employee', $employeeCount) }}
                &bull; Remaining to mark: <strong class="text-warning-emphasis">{{ $todayAttendance['pending'] }}</strong>
            </div>
            @if ($employeeCount > 0)
                <div>
                    Marked Coverage: <strong>{{ round(($todayAttendance['total'] / $employeeCount) * 100) }}%</strong>
                </div>
            @endif
        </div>
    </div>
</div>

        <div class="mt-3 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center text-muted small">
            <div>
                <i class="bi bi-info-circle me-1 text-primary"></i>
                Total marked today: <strong>{{ $todayAttendance['total'] }}</strong> of <strong>{{ $employeeCount }}</strong> {{ Str::plural('employee', $employeeCount) }}
            </div>
            @if ($employeeCount > 0)
                <div>
                    Marked Coverage: <strong>{{ round(($todayAttendance['total'] / $employeeCount) * 100) }}%</strong>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Employees Table -->
    <div class="col-lg-8">
        <div class="card card-custom h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history me-2 text-primary"></i> Recently Added Employees
                </h5>
                <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-primary">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Emp ID</th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Joined</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentEmployees as $emp)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace">{{ $emp->employee_id }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $emp->name }}</div>
                                        <div class="text-muted small">{{ $emp->email }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary px-2 py-1 rounded-pill">
                                            {{ $emp->department->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $emp->designation ?? '—' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($emp->joining_date)->format('M d, Y') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('employees.show', $emp) }}" class="btn btn-sm btn-light border" title="View Profile">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('employees.edit', $emp) }}" class="btn btn-sm btn-light border" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                        No employees found yet. Click <strong>Add Employee</strong> to start!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Distribution Card -->
    <div class="col-lg-4">
        <div class="card card-custom h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-bar-chart-steps me-2 text-primary"></i> Department Breakdown
                </h5>
                <a href="{{ route('departments.index') }}" class="btn btn-sm btn-outline-primary">
                    Manage
                </a>
            </div>
            <div class="card-body">
                @forelse ($departmentStats as $dept)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <div class="fw-semibold text-dark">{{ $dept->name }}</div>
                            <small class="text-muted">
                                <a href="{{ route('employees.index', ['department_id' => $dept->id]) }}" class="text-decoration-none">
                                    Filter employees <i class="bi bi-chevron-right"></i>
                                </a>
                            </small>
                        </div>
                        <div>
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2 fw-bold">
                                {{ $dept->employees_count }} {{ Str::plural('staff', $dept->employees_count) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        No departments created yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

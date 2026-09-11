@extends('layouts.ems')

@section('title', 'Attendance Management')

@section('content')
<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                <i class="bi bi-calendar-check fs-4"></i>
            </div>
            <div>
                <h2 class="h3 fw-bold text-dark mb-0">Attendance Management</h2>
                <p class="text-muted mb-0 small">View and mark attendance for all employees</p>
            </div>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0 small">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none"><i class="bi bi-house-door me-1"></i> Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Attendance</li>
        </ol>
    </nav>
</div>

<!-- 6 Top Summary KPI Cards -->
<div class="row g-3 mb-4">
    <!-- Total Employees -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card card-custom h-100 border-0 shadow-sm p-3 position-relative overflow-hidden">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background-color: #e0f2fe; color: #0284c7;">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem;">Total Employees</div>
                    <div class="h3 fw-bold text-dark mb-0">{{ $totalEmployees }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Present -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card card-custom h-100 border-0 shadow-sm p-3 position-relative overflow-hidden">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background-color: #dcfce7; color: #16a34a;">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem;">Present</div>
                    <div class="h3 fw-bold text-dark mb-0">{{ $presentCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Absent -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card card-custom h-100 border-0 shadow-sm p-3 position-relative overflow-hidden">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background-color: #fee2e2; color: #dc2626;">
                    <i class="bi bi-x-circle-fill fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem;">Absent</div>
                    <div class="h3 fw-bold text-dark mb-0">{{ $absentCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Half Day -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card card-custom h-100 border-0 shadow-sm p-3 position-relative overflow-hidden">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background-color: #fef9c3; color: #ca8a04;">
                    <i class="bi bi-clock-fill fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem;">Half Day</div>
                    <div class="h3 fw-bold text-dark mb-0">{{ $halfDayCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card card-custom h-100 border-0 shadow-sm p-3 position-relative overflow-hidden">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background-color: #f3e8ff; color: #9333ea;">
                    <i class="bi bi-calendar2-week-fill fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem;">Leave</div>
                    <div class="h3 fw-bold text-dark mb-0">{{ $leaveCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card card-custom h-100 border-0 shadow-sm p-3 position-relative overflow-hidden">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background-color: #ffedd5; color: #ea580c;">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem;">Pending</div>
                    <div class="h3 fw-bold text-dark mb-0">{{ $pendingCount }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card card-custom mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('attendances.index') }}" class="row g-2 align-items-end">
            <!-- Date Filter -->
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label for="date" class="form-label small fw-semibold text-muted mb-1">Date</label>
                <div class="input-group">
                    <input type="date"
                           name="date"
                           id="date"
                           class="form-control form-control-sm"
                           value="{{ $selectedDate }}">
                </div>
            </div>

            <!-- Department Filter -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <label for="department_id" class="form-label small fw-semibold text-muted mb-1">Department</label>
                <select name="department_id" id="department_id" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label for="status" class="form-label small fw-semibold text-muted mb-1">Status</label>
                <select name="status" id="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $stat)
                        <option value="{{ $stat }}" {{ request('status') == $stat ? 'selected' : '' }}>
                            {{ $stat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Search Filter -->
            <div class="col-lg-3 col-md-8 col-sm-6">
                <label for="search" class="form-label small fw-semibold text-muted mb-1">Search</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                    <input type="text"
                           name="search"
                           id="search"
                           class="form-control"
                           placeholder="Search by ID, name, email..."
                           value="{{ request('search') }}">
                </div>
            </div>

            <!-- Buttons -->
            <div class="col-lg-2 col-md-4 col-sm-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-funnel-fill me-1"></i> Filter
                </button>
                <a href="{{ route('attendances.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Employee Attendance Table Card -->
<div class="card card-custom border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Employee Attendance</h5>
            <small class="text-muted">Showing all employees with their attendance status for <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('d-m-Y') }}</strong></small>
        </div>
        <div>
            <a href="{{ route('attendances.create', ['date' => $selectedDate]) }}" class="btn btn-success btn-sm px-3 fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Mark Attendance
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.92rem;">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th style="width: 60px;">Photo</th>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Remarks</th>
                        <th class="text-center" style="width: 140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        @php
                            $att = $employee->attendanceForDate($selectedDate);
                        @endphp
                        <tr>
                            <!-- Row Number -->
                            <td class="text-center text-muted fw-semibold">
                                {{ $employees->firstItem() + $loop->index }}
                            </td>

                            <!-- Photo -->
                            <td>
                                @if ($employee->profile_image_url)
                                    <img src="{{ $employee->profile_image_url }}"
                                         alt="{{ $employee->name }}"
                                         class="rounded-circle shadow-sm"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center shadow-sm"
                                         style="width: 40px; height: 40px; font-size: 0.9rem;">
                                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>

                            <!-- Employee ID -->
                            <td>
                                <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                    {{ $employee->employee_id }}
                                </span>
                            </td>

                            <!-- Employee Name -->
                            <td>
                                <a href="{{ route('employees.show', $employee) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $employee->name }}
                                </a>
                            </td>

                            <!-- Department -->
                            <td>
                                <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: #e0f2fe; color: #0369a1;">
                                    {{ $employee->department->name ?? 'None' }}
                                </span>
                            </td>

                            <!-- Email -->
                            <td class="text-muted small">
                                {{ $employee->email }}
                            </td>

                            <!-- Status Badge -->
                            <td>
                                @if ($att)
                                    @if ($att->status === 'Present')
                                        <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background-color: #16a34a; color: #fff;">
                                            <i class="bi bi-check-circle me-1"></i> Present
                                        </span>
                                    @elseif ($att->status === 'Half Day')
                                        <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background-color: #eab308; color: #000;">
                                            <i class="bi bi-clock me-1"></i> Half Day
                                        </span>
                                    @elseif ($att->status === 'Absent')
                                        <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background-color: #dc2626; color: #fff;">
                                            <i class="bi bi-x-circle me-1"></i> Absent
                                        </span>
                                    @elseif ($att->status === 'Leave')
                                        <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background-color: #9333ea; color: #fff;">
                                            <i class="bi bi-calendar2-range me-1"></i> Leave
                                        </span>
                                    @endif
                                @else
                                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background-color: #ea580c; color: #fff;">
                                        <i class="bi bi-hourglass-split me-1"></i> Pending
                                    </span>
                                @endif
                            </td>

                            <!-- Check In -->
                            <td class="font-monospace text-muted">
                                {{ $att ? $att->formatted_check_in : '—' }}
                            </td>

                            <!-- Check Out -->
                            <td class="font-monospace text-muted">
                                {{ $att ? $att->formatted_check_out : '—' }}
                            </td>

                            <!-- Remarks -->
                            <td class="text-muted small">
                                {{ $att && $att->remarks ? $att->remarks : '—' }}
                            </td>

                            <!-- Action -->
                            <td class="text-center">
                                @if (! $att)
                                    <!-- Pending: Show prominent Mark button -->
                                    <a href="{{ route('attendances.create', ['employee_id' => $employee->id, 'date' => $selectedDate]) }}"
                                       class="btn btn-sm btn-success px-3 fw-semibold shadow-sm"
                                       title="Mark Attendance">
                                        <i class="bi bi-check2-circle me-1"></i> Mark
                                    </a>
                                @else
                                    <!-- Marked: Show Edit button and Delete option -->
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('attendances.edit', $att) }}"
                                           class="btn btn-sm btn-primary px-3 fw-semibold shadow-sm"
                                           title="Edit Attendance">
                                            <i class="bi bi-pencil-square me-1"></i> Edit
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteAttModal{{ $att->id }}"
                                                title="Delete Record">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteAttModal{{ $att->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered text-start">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-danger">
                                                        <i class="bi bi-exclamation-octagon me-1"></i> Confirm Delete
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body py-3">
                                                    Are you sure you want to remove attendance for <strong>{{ $employee->name }}</strong> on <strong>{{ $att->formatted_date }}</strong>?
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0">
                                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('attendances.destroy', $att) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary"></i>
                                No employees found matching the selected criteria.
                                @if (request()->hasAny(['search', 'department_id', 'status']))
                                    <div class="mt-2">
                                        <a href="{{ route('attendances.index', ['date' => $selectedDate]) }}" class="btn btn-sm btn-outline-primary">
                                            Clear Filters
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($employees->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex flex-wrap justify-content-between align-items-center">
            <div class="small text-muted mb-2 mb-md-0">
                Showing {{ $employees->firstItem() ?? 0 }} to {{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }} employees
            </div>
            <div>
                {{ $employees->links() }}
            </div>
        </div>
    @else
        <div class="card-footer bg-white border-top py-3 text-muted small">
            Showing {{ $employees->total() }} {{ Str::plural('employee', $employees->total()) }}
        </div>
    @endif
</div>
@endsection

@extends('layouts.ems')

@section('title', 'Employee Attendance History')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <div class="d-flex align-items-center">
            <a href="{{ route('attendances.index') }}" class="btn btn-sm btn-light border me-2" title="Back to Attendance">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="h3 fw-bold text-dark mb-0">Attendance History</h2>
                <p class="text-muted mb-0 small">Monthly attendance logs and performance for <strong>{{ $employee->name }}</strong></p>
            </div>
        </div>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <a href="{{ route('attendances.create', ['employee_id' => $employee->id]) }}" class="btn btn-success me-2 btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Mark Attendance
        </a>
        <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-person me-1"></i> Profile
        </a>
    </div>
</div>

<!-- Employee Header Card with Photo -->
<div class="card card-custom mb-4 overflow-hidden border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8 d-flex align-items-center">
                @if ($employee->profile_image_url)
                    <img src="{{ $employee->profile_image_url }}" alt="{{ $employee->name }}" class="rounded-circle shadow-sm me-3 border" style="width: 58px; height: 58px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 58px; height: 58px; font-size: 1.4rem;">
                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h4 class="fw-bold text-dark mb-1">{{ $employee->name }}</h4>
                    <div class="text-muted small">
                        <span class="badge bg-light text-dark border font-monospace me-2">{{ $employee->employee_id }}</span>
                        <span class="badge rounded-pill px-3 py-1 me-2" style="background-color: #e0f2fe; color: #0369a1;">
                            {{ $employee->department->name ?? 'No Department' }}
                        </span>
                        <span>{{ $employee->designation ?? 'Staff' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <!-- Month Filter Form -->
                <form action="{{ route('employees.attendance', $employee) }}" method="GET" class="d-inline-flex align-items-center gap-2">
                    <label for="month" class="small fw-semibold text-muted text-nowrap">Month:</label>
                    <input type="month" name="month" id="month" class="form-control form-control-sm" value="{{ $currentMonth }}" onchange="this.form.submit()">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Summary KPI Row -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-6">
        <div class="card card-custom p-3 border-start border-success border-4 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase">Present Days</div>
            <div class="h3 fw-bold text-success mb-0">{{ $presentCount }}</div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="card card-custom p-3 border-start border-danger border-4 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase">Absent Days</div>
            <div class="h3 fw-bold text-danger mb-0">{{ $absentCount }}</div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="card card-custom p-3 border-start border-warning border-4 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase">Half Days</div>
            <div class="h3 fw-bold text-warning mb-0">{{ $halfDayCount }}</div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="card card-custom p-3 border-start border-info border-4 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase">Leaves</div>
            <div class="h3 fw-bold text-info mb-0">{{ $leaveCount }}</div>
        </div>
    </div>
</div>

<!-- Attendance Percentage Overview Card -->
<div class="card card-custom mb-4 border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-pie-chart me-1 text-primary"></i> Monthly Attendance Rate
                </h5>
                <div class="text-muted small">
                    Formula: <code>(Present + Half Day &times; 0.5) &divide; (Present + Half Day + Absent) &times; 100</code>
                </div>
            </div>
            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                <span class="h2 fw-bold text-primary mb-0">{{ $percentage }}%</span>
                <span class="text-muted small ms-1">({{ $applicableDays }} applicable days)</span>
            </div>
        </div>

        <div class="progress mt-3" style="height: 10px;">
            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $percentage) }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
</div>

<!-- Detailed Daily Records for Month -->
<div class="card card-custom border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-calendar-range me-2 text-primary"></i> {{ \Carbon\Carbon::createFromFormat('Y-m', $currentMonth)->format('F Y') }} Attendance Log
        </h5>
        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
            {{ $attendances->count() }} Days Logged
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th style="width: 140px;">Date</th>
                        <th>Day</th>
                        <th>Status</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Remarks</th>
                        <th class="text-end" style="width: 120px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $att)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark">{{ $att->formatted_date }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ \Carbon\Carbon::parse($att->attendance_date)->format('l') }}</span>
                            </td>
                            <td>
                                @if ($att->status === 'Present')
                                    <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: #16a34a; color: #fff;">Present</span>
                                @elseif ($att->status === 'Half Day')
                                    <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: #eab308; color: #000;">Half Day</span>
                                @elseif ($att->status === 'Absent')
                                    <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: #dc2626; color: #fff;">Absent</span>
                                @elseif ($att->status === 'Leave')
                                    <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: #9333ea; color: #fff;">Leave</span>
                                @endif
                            </td>
                            <td>
                                <span class="font-monospace text-muted">{{ $att->formatted_check_in }}</span>
                            </td>
                            <td>
                                <span class="font-monospace text-muted">{{ $att->formatted_check_out }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $att->remarks ?: '—' }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('attendances.edit', $att) }}" class="btn btn-sm btn-light border" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                                No attendance records found for this month.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

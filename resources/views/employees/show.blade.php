@extends('layouts.ems')

@section('title', 'Employee Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('employees.index') }}" class="btn btn-sm btn-light border me-2">
                    <i class="bi bi-arrow-left"></i> Back to Directory
                </a>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('employees.attendance', $employee) }}" class="btn btn-outline-success">
                    <i class="bi bi-calendar-check me-1"></i> Attendance History
                </a>
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil-square me-1"></i> Edit Profile
                </a>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteEmployeeModal">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        </div>

        <!-- Header Profile Card -->
        <div class="card card-custom mb-4 overflow-hidden">
            <div class="bg-primary p-4 text-white position-relative" style="background: linear-gradient(135deg, #4f46e5 0%, #1e1b4b 100%) !important;">
                <div class="d-flex align-items-center">
                    @if ($employee->profile_image_url)
                        <img src="{{ $employee->profile_image_url }}" alt="{{ $employee->name }}" class="rounded-circle border border-white border-2 shadow me-3" style="width: 64px; height: 64px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-white text-primary fw-bold d-flex align-items-center justify-content-center me-3 shadow" style="width: 64px; height: 64px; font-size: 1.5rem;">
                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h3 class="fw-bold mb-0 text-white">{{ $employee->name }}</h3>
                        <div class="opacity-75 fs-6">{{ $employee->designation ?? 'Employee' }} &bull; <span class="badge bg-light text-dark font-monospace">{{ $employee->employee_id }}</span></div>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Column 1: Employment Details -->
                    <div class="col-md-6">
                        <h6 class="fw-bold text-uppercase text-muted small mb-3 border-bottom pb-2">
                            <i class="bi bi-building me-1 text-primary"></i> Employment Information
                        </h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <th class="text-muted fw-normal" style="width: 40%;">Department:</th>
                                <td class="fw-semibold">
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">
                                        {{ $employee->department->name ?? 'None' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Designation:</th>
                                <td class="fw-semibold text-dark">{{ $employee->designation ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Joining Date:</th>
                                <td class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($employee->joining_date)->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Salary:</th>
                                <td class="fw-semibold text-dark">
                                    {{ $employee->salary ? '$' . number_format($employee->salary, 2) : 'Not Specified' }}
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Column 2: Contact & Personal -->
                    <div class="col-md-6">
                        <h6 class="fw-bold text-uppercase text-muted small mb-3 border-bottom pb-2">
                            <i class="bi bi-person-lines-fill me-1 text-primary"></i> Contact & Personal
                        </h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <th class="text-muted fw-normal" style="width: 40%;">Email:</th>
                                <td><a href="mailto:{{ $employee->email }}" class="text-decoration-none fw-semibold">{{ $employee->email }}</a></td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Phone:</th>
                                <td class="fw-semibold text-dark">{{ $employee->phone }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Gender:</th>
                                <td class="fw-semibold text-dark">{{ $employee->gender ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Date of Birth:</th>
                                <td class="fw-semibold text-dark">
                                    @if ($employee->date_of_birth)
                                        {{ \Carbon\Carbon::parse($employee->date_of_birth)->format('F d, Y') }}
                                        <span class="text-muted small">({{ \Carbon\Carbon::parse($employee->date_of_birth)->age }} yrs)</span>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Address:</th>
                                <td class="fw-semibold text-dark">{{ $employee->address ?? '—' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteEmployeeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered text-start">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger">
                            <i class="bi bi-exclamation-octagon me-1"></i> Confirm Delete
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-3">
                        Are you sure you want to remove <strong>{{ $employee->name }}</strong> (<code>{{ $employee->employee_id }}</code>)?
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

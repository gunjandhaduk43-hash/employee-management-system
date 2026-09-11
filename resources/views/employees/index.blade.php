@extends('layouts.ems')

@section('title', 'Employees')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="h3 fw-bold text-dark mb-1">Employee Directory</h2>
        <p class="text-muted mb-0">Search, filter, and manage organizational staff records.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i> Add Employee
        </a>
    </div>
</div>

<!-- Filter & Search Bar -->
<div class="card card-custom mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('employees.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text"
                           name="search"
                           class="form-control border-start-0 ps-0"
                           placeholder="Search by Employee ID, Name, or Email..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="department_id" class="form-select">
                    <option value="">All Departments</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel-fill me-1"></i> Filter
                </button>
                @if (request()->hasAny(['search', 'department_id']))
                    <a href="{{ route('employees.index') }}" class="btn btn-light border" title="Clear Filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Employees Table -->
<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Contact</th>
                        <th>Joining Date</th>
                        <th class="text-end" style="width: 190px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                    {{ $employee->employee_id }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if ($employee->profile_image_url)
                                        <img src="{{ $employee->profile_image_url }}" alt="{{ $employee->name }}" class="rounded-circle shadow-sm me-2" style="width: 38px; height: 38px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px; font-size: 0.85rem;">
                                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('employees.show', $employee) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $employee->name }}
                                        </a>
                                        <div class="text-muted small">{{ $employee->gender ?? 'Unspecified' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-semibold">
                                    {{ $employee->department->name ?? 'None' }}
                                </span>
                            </td>
                            <td>{{ $employee->designation ?? '—' }}</td>
                            <td>
                                <div class="small">
                                    <i class="bi bi-envelope text-muted me-1"></i> {{ $employee->email }}
                                </div>
                                <div class="small text-muted">
                                    <i class="bi bi-telephone text-muted me-1"></i> {{ $employee->phone }}
                                </div>
                            </td>
                            <td>
                                <span class="small">{{ \Carbon\Carbon::parse($employee->joining_date)->format('M d, Y') }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('employees.attendance', $employee) }}" class="btn btn-sm btn-light border text-primary" title="Attendance History">
                                    <i class="bi bi-calendar-check"></i>
                                </a>
                                <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-light border" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-light border" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteEmployeeModal{{ $employee->id }}" title="Delete Employee">
                                    <i class="bi bi-trash"></i>
                                </button>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteEmployeeModal{{ $employee->id }}" tabindex="-1" aria-hidden="true">
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
                                                This action cannot be undone.
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary"></i>
                                No employees found matching the criteria.
                                @if (request()->hasAny(['search', 'department_id']))
                                    <div class="mt-2">
                                        <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-primary">Reset Filters</a>
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
        <div class="card-footer bg-white border-top py-3">
            {{ $employees->links() }}
        </div>
    @endif
</div>
@endsection

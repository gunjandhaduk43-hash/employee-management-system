@extends('layouts.ems')

@section('title', 'Departments')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="h3 fw-bold text-dark mb-1">Department Management</h2>
        <p class="text-muted mb-0">Organize and manage departments and view employee headcounts.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <a href="{{ route('departments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add Department
        </a>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 80px;">#</th>
                        <th>Department Name</th>
                        <th>Total Employees</th>
                        <th>Created At</th>
                        <th class="text-end" style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($departments as $dept)
                        <tr>
                            <td><span class="text-muted fw-semibold">{{ $loop->iteration + ($departments->currentPage() - 1) * $departments->perPage() }}</span></td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $dept->name }}</div>
                            </td>
                            <td>
                                <a href="{{ route('employees.index', ['department_id' => $dept->id]) }}" class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 text-decoration-none fw-semibold">
                                    <i class="bi bi-people me-1"></i> {{ $dept->employees_count }} {{ Str::plural('Employee', $dept->employees_count) }}
                                </a>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $dept->created_at ? $dept->created_at->format('M d, Y') : '—' }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('departments.edit', $dept) }}" class="btn btn-sm btn-outline-secondary me-1" title="Edit Department">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>

                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $dept->id }}" title="Delete Department">
                                    <i class="bi bi-trash"></i>
                                </button>

                                <!-- Delete Confirmation Modal -->
                                <div class="modal fade" id="deleteModal{{ $dept->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $dept->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header border-bottom-0 pb-0">
                                                <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel{{ $dept->id }}">
                                                    <i class="bi bi-exclamation-octagon me-1"></i> Confirm Delete
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body py-3">
                                                <p class="mb-2">Are you sure you want to delete the department <strong>{{ $dept->name }}</strong>?</p>
                                                @if ($dept->employees_count > 0)
                                                    <div class="alert alert-warning py-2 px-3 small mb-0">
                                                        <i class="bi bi-shield-exclamation me-1"></i> This department currently has <strong>{{ $dept->employees_count }}</strong> assigned employees. You must reassign or remove them before deleting.
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer border-top-0 pt-0">
                                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('departments.destroy', $dept) }}" method="POST" class="d-inline">
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
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-building-slash fs-1 d-block mb-2 text-secondary"></i>
                                No departments found. Click <strong>Add Department</strong> to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($departments->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $departments->links() }}
        </div>
    @endif
</div>
@endsection

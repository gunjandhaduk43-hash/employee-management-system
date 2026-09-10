@extends('layouts.ems')

@section('title', 'Add Department')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="d-flex align-items-center mb-3">
            <a href="{{ route('departments.index') }}" class="btn btn-sm btn-light border me-2">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h3 class="h4 fw-bold mb-0 text-dark">Add New Department</h3>
        </div>

        <div class="card card-custom">
            <div class="card-body p-4">
                <form action="{{ route('departments.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Department Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name"
                               id="name"
                               class="form-control form-control-lg @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="e.g. Information Technology"
                               required
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="form-text">Must be unique across the organization.</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('departments.index') }}" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2-circle me-1"></i> Save Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

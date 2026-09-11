@extends('layouts.ems')

@section('title', 'Edit Employee')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex align-items-center mb-3">
            <a href="{{ route('employees.index') }}" class="btn btn-sm btn-light border me-2">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h3 class="h4 fw-bold mb-0 text-dark">Edit Employee: {{ $employee->name }}</h3>
        </div>

        <div class="card card-custom">
            <div class="card-body p-4">
                <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <h5 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                        <i class="bi bi-briefcase me-1"></i> Employment Details
                    </h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="employee_id" class="form-label fw-semibold">Employee ID <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="employee_id"
                                   id="employee_id"
                                   class="form-control @error('employee_id') is-invalid @enderror"
                                   value="{{ old('employee_id', $employee->employee_id) }}"
                                   required>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="department_id" class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                            <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="designation" class="form-label fw-semibold">Designation</label>
                            <input type="text"
                                   name="designation"
                                   id="designation"
                                   class="form-control @error('designation') is-invalid @enderror"
                                   value="{{ old('designation', $employee->designation) }}">
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="joining_date" class="form-label fw-semibold">Joining Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   name="joining_date"
                                   id="joining_date"
                                   class="form-control @error('joining_date') is-invalid @enderror"
                                   value="{{ old('joining_date', optional($employee->joining_date)->format('Y-m-d')) }}"
                                   required>
                            @error('joining_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="salary" class="form-label fw-semibold">Salary</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       step="0.01"
                                       name="salary"
                                       id="salary"
                                       class="form-control @error('salary') is-invalid @enderror"
                                       value="{{ old('salary', $employee->salary) }}">
                                @error('salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                        <i class="bi bi-camera me-1"></i> Profile Photo
                    </h5>

                    <div class="row g-3 mb-4 align-items-center">
                        <div class="col-md-3 text-center text-md-start">
                            @if ($employee->profile_image_url)
                                <img src="{{ $employee->profile_image_url }}"
                                     alt="{{ $employee->name }}"
                                     class="rounded-circle shadow-sm border"
                                     style="width: 72px; height: 72px; object-fit: cover;">
                                <div class="small text-muted mt-1">Current Photo</div>
                            @else
                                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-inline-flex align-items-center justify-content-center shadow-sm"
                                     style="width: 72px; height: 72px; font-size: 1.5rem;">
                                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                                </div>
                                <div class="small text-muted mt-1">Default Avatar</div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <label for="profile_image" class="form-label fw-semibold">Change Photo</label>
                            <input type="file"
                                   name="profile_image"
                                   id="profile_image"
                                   class="form-control @error('profile_image') is-invalid @enderror"
                                   accept="image/png, image/jpeg, image/jpg">
                            <div class="form-text">Upload to replace existing photo. Formats: JPG, JPEG, PNG (Max: 2MB).</div>
                            @error('profile_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h5 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                        <i class="bi bi-person me-1"></i> Personal & Contact Info
                    </h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $employee->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Work Email <span class="text-danger">*</span></label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $employee->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="phone"
                                   id="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $employee->phone) }}"
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="gender" class="form-label fw-semibold">Gender</label>
                            <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">-- Select Gender --</option>
                                <option value="Male" {{ old('gender', $employee->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $employee->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender', $employee->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label fw-semibold">Date of Birth</label>
                            <input type="date"
                                   name="date_of_birth"
                                   id="date_of_birth"
                                   class="form-control @error('date_of_birth') is-invalid @enderror"
                                   value="{{ old('date_of_birth', optional($employee->date_of_birth)->format('Y-m-d')) }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="address" class="form-label fw-semibold">Residential Address</label>
                            <textarea name="address"
                                      id="address"
                                      rows="3"
                                      class="form-control @error('address') is-invalid @enderror">{{ old('address', $employee->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('employees.index') }}" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2-circle me-1"></i> Update Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

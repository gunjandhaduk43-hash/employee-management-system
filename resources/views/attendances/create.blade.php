@extends('layouts.ems')

@section('title', 'Mark Attendance')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center mb-3">
            <a href="{{ route('attendances.index', ['date' => $selectedDate]) }}" class="btn btn-sm btn-light border me-2">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h3 class="h4 fw-bold mb-0 text-dark">Mark Attendance</h3>
                <p class="text-muted small mb-0">Record daily attendance status and working hours</p>
            </div>
        </div>

        <div class="card card-custom border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('attendances.store') }}" method="POST" id="attendanceForm">
                    @csrf

                    <!-- Employee Selection -->
                    <div class="mb-3">
                        <label for="employee_id" class="form-label fw-semibold">Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                            <option value="">-- Select Employee --</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}"
                                    {{ old('employee_id', $selectedEmployeeId) == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }} ({{ $emp->employee_id }}) — {{ $emp->department->name ?? 'No Dept' }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Date Selection -->
                    <div class="mb-3">
                        <label for="attendance_date" class="form-label fw-semibold">Attendance Date <span class="text-danger">*</span></label>
                        <input type="date"
                               name="attendance_date"
                               id="attendance_date"
                               class="form-control @error('attendance_date') is-invalid @enderror"
                               value="{{ old('attendance_date', $selectedDate) }}"
                               required>
                        @error('attendance_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status Selection -->
                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Attendance Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            @foreach ($statuses as $stat)
                                <option value="{{ $stat }}" {{ old('status', 'Present') == $stat ? 'selected' : '' }}>
                                    {{ $stat }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text" id="status-help-text">
                            Present & Half Day allow Check In / Check Out. Absent & Leave automatically disable and clear times.
                        </div>
                    </div>

                    <!-- Check In & Check Out Times (12-hour display / HTML time input) -->
                    <div class="row g-3 mb-3" id="time-inputs-container">
                        <div class="col-md-6">
                            <label for="check_in" class="form-label fw-semibold">Check In Time</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                <input type="time"
                                       name="check_in"
                                       id="check_in"
                                       class="form-control @error('check_in') is-invalid @enderror"
                                       value="{{ old('check_in', '09:00') }}">
                            </div>
                            <div class="small text-muted mt-1" id="check_in_12hr">Display format: 09:00 AM</div>
                            @error('check_in')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="check_out" class="form-label fw-semibold">Check Out Time</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-clock-history"></i></span>
                                <input type="time"
                                       name="check_out"
                                       id="check_out"
                                       class="form-control @error('check_out') is-invalid @enderror"
                                       value="{{ old('check_out', '17:30') }}">
                            </div>
                            <div class="small text-muted mt-1" id="check_out_12hr">Display format: 05:30 PM</div>
                            @error('check_out')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div class="mb-4">
                        <label for="remarks" class="form-label fw-semibold">Remarks (Optional)</label>
                        <textarea name="remarks"
                                  id="remarks"
                                  rows="3"
                                  class="form-control @error('remarks') is-invalid @enderror"
                                  placeholder="e.g. Regular working shift, doctor appointment, casual leave approved...">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('attendances.index', ['date' => $selectedDate]) }}" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-success px-4 fw-semibold">
                            <i class="bi bi-check-circle me-1"></i> Save Attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusSelect = document.getElementById('status');
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const checkIn12Hr = document.getElementById('check_in_12hr');
    const checkOut12Hr = document.getElementById('check_out_12hr');

    function formatTo12Hr(time24) {
        if (!time24) return '';
        const parts = time24.split(':');
        let hours = parseInt(parts[0], 10);
        const minutes = parts[1] || '00';
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // 0 becomes 12
        const strHours = hours < 10 ? '0' + hours : hours;
        return strHours + ':' + minutes + ' ' + ampm;
    }

    function updateTimeHints() {
        if (checkIn12Hr && checkInInput) {
            checkIn12Hr.textContent = checkInInput.value ? '12-hr: ' + formatTo12Hr(checkInInput.value) : '12-hr: —';
        }
        if (checkOut12Hr && checkOutInput) {
            checkOut12Hr.textContent = checkOutInput.value ? '12-hr: ' + formatTo12Hr(checkOutInput.value) : '12-hr: —';
        }
    }

    function syncTimeInputs() {
        const status = statusSelect.value;
        const requiresTimes = (status === 'Present' || status === 'Half Day');

        if (requiresTimes) {
            checkInInput.disabled = false;
            checkOutInput.disabled = false;
            updateTimeHints();
        } else {
            // Absent or Leave: disable and clear times
            checkInInput.value = '';
            checkOutInput.value = '';
            checkInInput.disabled = true;
            checkOutInput.disabled = true;
            if (checkIn12Hr) checkIn12Hr.textContent = 'Times not applicable for ' + status;
            if (checkOut12Hr) checkOut12Hr.textContent = 'Times not applicable for ' + status;
        }
    }

    if (statusSelect && checkInInput && checkOutInput) {
        statusSelect.addEventListener('change', syncTimeInputs);
        checkInInput.addEventListener('input', updateTimeHints);
        checkOutInput.addEventListener('input', updateTimeHints);
        syncTimeInputs();
    }
});
</script>
@endsection

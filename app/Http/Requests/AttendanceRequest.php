<?php

namespace App\Http\Requests;

use App\Models\Attendance;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'attendance_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['Present', 'Absent', 'Half Day', 'Leave'])],
            'check_in' => ['nullable', 'date_format:H:i,H:i:s'],
            'check_out' => ['nullable', 'date_format:H:i,H:i:s'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $employeeId = $this->input('employee_id');
            $attendanceDate = $this->input('attendance_date');
            $attendance = $this->route('attendance');
            $attendanceId = $attendance instanceof Attendance ? $attendance->id : $attendance;

            // Prevent duplicate attendance for the same employee and date
            if ($employeeId && $attendanceDate) {
                $exists = Attendance::where('employee_id', $employeeId)
                    ->whereDate('attendance_date', $attendanceDate)
                    ->when($attendanceId, fn ($query) => $query->where('id', '!=', $attendanceId))
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'attendance_date',
                        'Attendance for this employee has already been marked for this date.'
                    );
                }
            }

            $status = $this->input('status');

            // If Present or Half Day, validate that check_out is after check_in if both are filled
            if (in_array($status, ['Present', 'Half Day'])) {
                $checkIn = $this->input('check_in');
                $checkOut = $this->input('check_out');

                if (! empty($checkIn) && ! empty($checkOut)) {
                    if (strtotime($checkOut) <= strtotime($checkIn)) {
                        $validator->errors()->add('check_out', 'Check Out time must be after Check In time.');
                    }
                }
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'employee_id.required' => 'Please select an employee.',
            'employee_id.exists' => 'Selected employee does not exist.',
            'attendance_date.required' => 'Attendance date is required.',
            'attendance_date.date' => 'Enter a valid attendance date.',
            'status.required' => 'Please select an attendance status.',
            'status.in' => 'Selected status must be Present, Absent, Half Day, or Leave.',
            'check_in.date_format' => 'Check In must be a valid time (HH:MM).',
            'check_out.date_format' => 'Check Out must be a valid time (HH:MM).',
            'remarks.max' => 'Remarks may not exceed 1000 characters.',
        ];
    }
}

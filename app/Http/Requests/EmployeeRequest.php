<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
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
        $employee = $this->route('employee');

        return [
            'employee_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employees', 'employee_id')->ignore($employee),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->ignore($employee),
            ],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'address' => ['nullable', 'string', 'max:2000'],
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'joining_date' => ['required', 'date'],
            'salary' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'designation' => ['nullable', 'string', 'max:255'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.unique' => 'This Employee ID is already in use.',
            'name.required' => 'Employee name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email address.',
            'email.unique' => 'This email is already assigned to another employee.',
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Enter a valid phone number.',
            'date_of_birth.before' => 'Date of birth must be a date in the past.',
            'joining_date.required' => 'Joining date is required.',
            'department_id.required' => 'Department is required.',
            'department_id.exists' => 'Select a valid department.',
            'profile_image.image' => 'The profile image must be an image file.',
            'profile_image.mimes' => 'The profile image must be a file of type: jpg, jpeg, png.',
            'profile_image.max' => 'The profile image may not be greater than 2MB.',
        ];
    }
}

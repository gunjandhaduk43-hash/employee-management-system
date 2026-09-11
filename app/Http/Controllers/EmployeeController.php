<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Employee::with('department');

        if ($request->filled('search')) {
            $search = trim((string) $request->string('search'));

            $query->where(function ($builder) use ($search) {
                $builder->where('employee_id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->integer('department_id'));
        }

        return view('employees.index', [
            'employees' => $query->latest()->paginate(10)->withQueryString(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('employees.create', [
            'employee' => new Employee,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function store(EmployeeRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('employees', 'public');
        }

        Employee::create($data);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee added successfully.');
    }

    public function show(Employee $employee): View
    {
        $employee->load('department');

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        return view('employees.edit', [
            'employee' => $employee,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function update(EmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('profile_image')) {
            if ($employee->profile_image && Storage::disk('public')->exists($employee->profile_image)) {
                Storage::disk('public')->delete($employee->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('employees', 'public');
        }

        $employee->update($data);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        if ($employee->attendances()->exists()) {
            return back()->with('error', 'This employee cannot be deleted because attendance records are associated with them.');
        }

        if ($employee->profile_image && Storage::disk('public')->exists($employee->profile_image)) {
            Storage::disk('public')->delete($employee->profile_image);
        }

        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}

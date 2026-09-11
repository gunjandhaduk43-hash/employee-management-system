<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin User
        User::updateOrCreate(
            ['email' => 'admin@ems.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. Seed Non-Admin Staff User (for testing unauthorized access)
        User::updateOrCreate(
            ['email' => 'staff@ems.com'],
            [
                'name' => 'Regular Staff',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        // 3. Seed Departments
        $departments = [
            'Engineering',
            'Human Resources',
            'Finance & Accounting',
            'Marketing & Sales',
            'Operations & Logistics',
            'IT Support',
            'Design',
        ];

        $deptModels = [];
        foreach ($departments as $name) {
            $deptModels[$name] = Department::firstOrCreate(['name' => $name]);
        }

        // 4. Seed Realistic Employees matching user reference design
        $sampleEmployees = [
            [
                'employee_id' => 'EMP-1001',
                'name' => 'Alice Johnson',
                'email' => 'alice.johnson@example.com',
                'phone' => '+1-555-0101',
                'address' => '123 Innovation Way, Tech City, CA',
                'gender' => 'Female',
                'date_of_birth' => '1992-04-15',
                'joining_date' => '2022-01-10',
                'salary' => 95000.00,
                'designation' => 'Senior Software Engineer',
                'department_id' => $deptModels['Engineering']->id,
            ],
            [
                'employee_id' => 'EMP-1002',
                'name' => 'Bob Smith',
                'email' => 'bob.smith@example.com',
                'phone' => '+1-555-0102',
                'address' => '456 Redwood Blvd, San Francisco, CA',
                'gender' => 'Male',
                'date_of_birth' => '1988-11-23',
                'joining_date' => '2021-06-01',
                'salary' => 110000.00,
                'designation' => 'Lead DevOps Architect',
                'department_id' => $deptModels['Engineering']->id,
            ],
            [
                'employee_id' => 'EMP-1003',
                'name' => 'Carol Williams',
                'email' => 'carol.williams@example.com',
                'phone' => '+1-555-0103',
                'address' => '789 Maple Ave, Austin, TX',
                'gender' => 'Female',
                'date_of_birth' => '1995-08-30',
                'joining_date' => '2023-03-15',
                'salary' => 72000.00,
                'designation' => 'HR Operations Specialist',
                'department_id' => $deptModels['Human Resources']->id,
            ],
            [
                'employee_id' => 'EMP-1004',
                'name' => 'David Lee',
                'email' => 'david.lee@example.com',
                'phone' => '+1-555-0104',
                'address' => '321 Pine St, Seattle, WA',
                'gender' => 'Male',
                'date_of_birth' => '1990-02-14',
                'joining_date' => '2020-09-01',
                'salary' => 88000.00,
                'designation' => 'Financial Analyst',
                'department_id' => $deptModels['Finance & Accounting']->id,
            ],
            [
                'employee_id' => 'EMP-1005',
                'name' => 'Emma Watson',
                'email' => 'emma.watson@example.com',
                'phone' => '+1-555-0105',
                'address' => '654 Elm Dr, New York, NY',
                'gender' => 'Female',
                'date_of_birth' => '1994-07-19',
                'joining_date' => '2022-11-01',
                'salary' => 78000.00,
                'designation' => 'Digital Marketing Manager',
                'department_id' => $deptModels['Marketing & Sales']->id,
            ],
            [
                'employee_id' => 'EMP-1006',
                'name' => 'Frank Miller',
                'email' => 'frank.miller@example.com',
                'phone' => '+1-555-0106',
                'address' => '987 Birch Lane, Chicago, IL',
                'gender' => 'Male',
                'date_of_birth' => '1987-12-05',
                'joining_date' => '2019-04-20',
                'salary' => 82000.00,
                'designation' => 'Systems Administrator',
                'department_id' => $deptModels['IT Support']->id,
            ],
            [
                'employee_id' => 'EMP-1007',
                'name' => 'Grace Wilson',
                'email' => 'grace.wilson@example.com',
                'phone' => '+1-555-0107',
                'address' => '555 Sunset Blvd, Los Angeles, CA',
                'gender' => 'Female',
                'date_of_birth' => '1996-03-22',
                'joining_date' => '2023-08-01',
                'salary' => 85000.00,
                'designation' => 'Product Designer',
                'department_id' => $deptModels['Design']->id,
            ],
            [
                'employee_id' => 'EMP-1008',
                'name' => 'Henry Brown',
                'email' => 'henry.brown@example.com',
                'phone' => '+1-555-0108',
                'address' => '777 Harbor View, Boston, MA',
                'gender' => 'Male',
                'date_of_birth' => '1991-09-18',
                'joining_date' => '2021-10-15',
                'salary' => 75000.00,
                'designation' => 'Operations Lead',
                'department_id' => $deptModels['Operations & Logistics']->id,
            ],
        ];

        $createdEmployees = [];
        foreach ($sampleEmployees as $emp) {
            $createdEmployees[$emp['employee_id']] = Employee::updateOrCreate(
                ['employee_id' => $emp['employee_id']],
                $emp
            );
        }

        // 5. Seed Attendance Records for Today
        // Note: EMP-1006 (Frank), EMP-1007 (Grace), EMP-1008 (Henry) have NO record -> Pending!
        $today = now()->toDateString();
        $sampleTodayAttendances = [
            [
                'employee_id' => $createdEmployees['EMP-1001']->id,
                'attendance_date' => $today,
                'status' => 'Present',
                'check_in' => '09:00:00',
                'check_out' => '17:30:00',
                'remarks' => 'Regular day',
            ],
            [
                'employee_id' => $createdEmployees['EMP-1002']->id,
                'attendance_date' => $today,
                'status' => 'Present',
                'check_in' => '08:45:00',
                'check_out' => '17:15:00',
                'remarks' => 'On-time arrival',
            ],
            [
                'employee_id' => $createdEmployees['EMP-1003']->id,
                'attendance_date' => $today,
                'status' => 'Half Day',
                'check_in' => '09:00:00',
                'check_out' => '13:00:00',
                'remarks' => 'Doctor appointment',
            ],
            [
                'employee_id' => $createdEmployees['EMP-1004']->id,
                'attendance_date' => $today,
                'status' => 'Absent',
                'check_in' => null,
                'check_out' => null,
                'remarks' => 'Unexcused absence',
            ],
            [
                'employee_id' => $createdEmployees['EMP-1005']->id,
                'attendance_date' => $today,
                'status' => 'Leave',
                'check_in' => null,
                'check_out' => null,
                'remarks' => 'Annual approved leave',
            ],
        ];

        foreach ($sampleTodayAttendances as $att) {
            $existing = Attendance::where('employee_id', $att['employee_id'])
                ->whereDate('attendance_date', $att['attendance_date'])
                ->first();

            if ($existing) {
                $existing->update($att);
            } else {
                Attendance::create($att);
            }
        }
    }
}

<?php

namespace Database\Seeders;

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
        ];

        $deptModels = [];
        foreach ($departments as $name) {
            $deptModels[$name] = Department::firstOrCreate(['name' => $name]);
        }

        // 4. Seed Realistic Employees
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
                'name' => 'Franklin Harris',
                'email' => 'franklin.harris@example.com',
                'phone' => '+1-555-0106',
                'address' => '987 Birch Lane, Chicago, IL',
                'gender' => 'Male',
                'date_of_birth' => '1987-12-05',
                'joining_date' => '2019-04-20',
                'salary' => 82000.00,
                'designation' => 'Supply Chain Coordinator',
                'department_id' => $deptModels['Operations & Logistics']->id,
            ],
        ];

        foreach ($sampleEmployees as $emp) {
            Employee::firstOrCreate(
                ['employee_id' => $emp['employee_id']],
                $emp
            );
        }
    }
}

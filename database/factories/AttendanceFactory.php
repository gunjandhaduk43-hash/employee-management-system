<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['Present', 'Absent', 'Half Day', 'Leave']);
        $hasTime = in_array($status, ['Present', 'Half Day']);

        return [
            'employee_id' => Employee::factory(),
            'attendance_date' => fake()->date(),
            'status' => $status,
            'check_in' => $hasTime ? '09:00' : null,
            'check_out' => $hasTime ? '18:00' : null,
            'remarks' => fake()->optional(0.3)->sentence(),
        ];
    }
}

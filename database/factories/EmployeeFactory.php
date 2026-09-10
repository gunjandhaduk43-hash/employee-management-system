<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'employee_id' => 'EMP-' . fake()->unique()->numberBetween(1000, 9999),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+1-' . fake()->numerify('###-###-####'),
            'address' => fake()->address(),
            'gender' => fake()->randomElement(['Male', 'Female', 'Other']),
            'date_of_birth' => fake()->dateTimeBetween('-55 years', '-21 years')->format('Y-m-d'),
            'joining_date' => fake()->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),
            'salary' => fake()->randomFloat(2, 45000, 140000),
            'designation' => fake()->jobTitle(),
            'department_id' => Department::factory(),
        ];
    }
}

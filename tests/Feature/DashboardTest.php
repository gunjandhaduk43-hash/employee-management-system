<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_dashboard_with_counts(): void
    {
        $admin = User::factory()->admin()->create();
        $dept = Department::factory()->create(['name' => 'IT Infrastructure']);
        Employee::factory()->create([
            'name' => 'Alice Administer',
            'department_id' => $dept->id,
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('System Overview');
        $response->assertSee('Total Employees');
        $response->assertSee('IT Infrastructure');
        $response->assertSee('Alice Administer');
    }
}

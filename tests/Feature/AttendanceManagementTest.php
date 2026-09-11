<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $nonAdmin;
    protected Department $department;
    protected Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->nonAdmin = User::factory()->create(['is_admin' => false]);
        $this->department = Department::factory()->create(['name' => 'Engineering']);
        $this->employee = Employee::factory()->create([
            'employee_id' => 'EMP-1001',
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'department_id' => $this->department->id,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_attendance_routes(): void
    {
        $this->get(route('attendances.index'))->assertRedirect(route('login'));
        $this->get(route('attendances.create'))->assertRedirect(route('login'));
        $this->post(route('attendances.store'), [])->assertRedirect(route('login'));
        $this->get(route('employees.attendance', $this->employee))->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_attendance_routes(): void
    {
        $this->actingAs($this->nonAdmin)->get(route('attendances.index'))->assertForbidden();
        $this->actingAs($this->nonAdmin)->get(route('attendances.create'))->assertForbidden();
        $this->actingAs($this->nonAdmin)->post(route('attendances.store'), [])->assertForbidden();
        $this->actingAs($this->nonAdmin)->get(route('employees.attendance', $this->employee))->assertForbidden();
    }

    public function test_admin_can_view_attendance_index_and_create_pages(): void
    {
        $response = $this->actingAs($this->admin)->get(route('attendances.index'));
        $response->assertOk();
        $response->assertSee('Attendance Management');
        $response->assertSee('Total Employees');
        $response->assertSee('Pending');

        $responseCreate = $this->actingAs($this->admin)->get(route('attendances.create'));
        $responseCreate->assertOk();
        $responseCreate->assertSee('Mark Attendance');
        $responseCreate->assertSee($this->employee->name);
    }

    public function test_all_employees_are_displayed_with_pending_status_when_unmarked(): void
    {
        $emp2 = Employee::factory()->create([
            'employee_id' => 'EMP-1002',
            'name' => 'Jane Smith',
            'department_id' => $this->department->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('attendances.index', ['date' => '2026-09-11']));
        $response->assertOk();

        // Both employees appear even with zero attendance records
        $response->assertSee('John Doe');
        $response->assertSee('Jane Smith');
        $response->assertSee('Pending');
        $response->assertSee('Mark');
    }

    public function test_admin_can_mark_attendance_present_with_times(): void
    {
        $data = [
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-11',
            'status' => 'Present',
            'check_in' => '09:00',
            'check_out' => '17:30',
            'remarks' => 'Regular working day',
        ];

        $response = $this->actingAs($this->admin)->post(route('attendances.store'), $data);

        $response->assertRedirect(route('attendances.index', ['date' => '2026-09-11']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-11 00:00:00',
            'status' => 'Present',
            'remarks' => 'Regular working day',
        ]);

        $attendance = Attendance::first();
        $this->assertNotNull($attendance->check_in);
        $this->assertNotNull($attendance->check_out);
        $this->assertEquals('09:00 AM', $attendance->formatted_check_in);
        $this->assertEquals('05:30 PM', $attendance->formatted_check_out);
    }

    public function test_admin_can_mark_attendance_absent_and_times_are_nullified(): void
    {
        $data = [
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-11',
            'status' => 'Absent',
            'check_in' => '09:00',
            'check_out' => '17:00',
            'remarks' => 'Unexcused absence',
        ];

        $response = $this->actingAs($this->admin)->post(route('attendances.store'), $data);

        $response->assertRedirect(route('attendances.index', ['date' => '2026-09-11']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
            'status' => 'Absent',
            'check_in' => null,
            'check_out' => null,
            'remarks' => 'Unexcused absence',
        ]);
    }

    public function test_admin_can_mark_attendance_half_day(): void
    {
        $data = [
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-11',
            'status' => 'Half Day',
            'check_in' => '09:00',
            'check_out' => '13:00',
            'remarks' => 'Doctor appointment',
        ];

        $response = $this->actingAs($this->admin)->post(route('attendances.store'), $data);

        $response->assertRedirect(route('attendances.index', ['date' => '2026-09-11']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
            'status' => 'Half Day',
        ]);
    }

    public function test_admin_can_mark_attendance_leave_and_times_are_nullified(): void
    {
        $data = [
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-11',
            'status' => 'Leave',
            'check_in' => '09:00',
            'check_out' => '17:00',
            'remarks' => 'Casual Leave approved',
        ];

        $response = $this->actingAs($this->admin)->post(route('attendances.store'), $data);

        $response->assertRedirect(route('attendances.index', ['date' => '2026-09-11']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
            'status' => 'Leave',
            'check_in' => null,
            'check_out' => null,
        ]);
    }

    public function test_cannot_mark_duplicate_attendance_for_same_employee_and_date(): void
    {
        Attendance::create([
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-11',
            'status' => 'Present',
            'check_in' => '09:00:00',
            'check_out' => '17:00:00',
        ]);

        $duplicateData = [
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-11',
            'status' => 'Absent',
        ];

        $response = $this->actingAs($this->admin)->post(route('attendances.store'), $duplicateData);

        $response->assertSessionHasErrors(['attendance_date']);
        $errors = session('errors');
        $this->assertEquals(
            'Attendance for this employee has already been marked for this date.',
            $errors->first('attendance_date')
        );

        $this->assertEquals(1, Attendance::where('employee_id', $this->employee->id)->count());
    }

    public function test_checkout_must_be_after_checkin_validation(): void
    {
        $invalidData = [
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-11',
            'status' => 'Present',
            'check_in' => '17:00',
            'check_out' => '09:00',
        ];

        $response = $this->actingAs($this->admin)->post(route('attendances.store'), $invalidData);

        $response->assertSessionHasErrors(['check_out']);
    }

    public function test_admin_can_edit_and_update_attendance(): void
    {
        $attendance = Attendance::create([
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-11',
            'status' => 'Absent',
            'check_in' => null,
            'check_out' => null,
            'remarks' => 'Initial absent',
        ]);

        $editPage = $this->actingAs($this->admin)->get(route('attendances.edit', $attendance));
        $editPage->assertOk();
        $editPage->assertSee('Edit Attendance');

        $updateData = [
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-11',
            'status' => 'Present',
            'check_in' => '09:15',
            'check_out' => '18:00',
            'remarks' => 'Updated to present with overtime',
        ];

        $response = $this->actingAs($this->admin)->put(route('attendances.update', $attendance), $updateData);

        $response->assertRedirect(route('attendances.index', ['date' => '2026-09-11']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 'Present',
            'remarks' => 'Updated to present with overtime',
        ]);
    }

    public function test_admin_can_delete_attendance(): void
    {
        $attendance = Attendance::create([
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-11',
            'status' => 'Present',
            'check_in' => '09:00:00',
            'check_out' => '17:00:00',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('attendances.destroy', $attendance));

        $response->assertRedirect(route('attendances.index', ['date' => '2026-09-11']));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('attendances', ['id' => $attendance->id]);
    }

    public function test_attendance_filtering_by_department_and_status(): void
    {
        $dept2 = Department::factory()->create(['name' => 'Human Resources']);
        $emp2 = Employee::factory()->create([
            'employee_id' => 'EMP-2002',
            'name' => 'Alice Bob',
            'department_id' => $dept2->id,
        ]);

        Attendance::create([
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-11',
            'status' => 'Present',
        ]);

        Attendance::create([
            'employee_id' => $emp2->id,
            'attendance_date' => '2026-09-11',
            'status' => 'Absent',
        ]);

        // Filter by Department
        $responseDept = $this->actingAs($this->admin)->get(route('attendances.index', ['date' => '2026-09-11', 'department_id' => $dept2->id]));
        $responseDept->assertSee($emp2->name);
        $responseDept->assertDontSee($this->employee->name);

        // Filter by Status: Absent
        $responseStatus = $this->actingAs($this->admin)->get(route('attendances.index', ['date' => '2026-09-11', 'status' => 'Absent']));
        $responseStatus->assertSee($emp2->name);
        $responseStatus->assertDontSee($this->employee->name);

        // Filter by Status: Pending
        $emp3 = Employee::factory()->create([
            'employee_id' => 'EMP-3003',
            'name' => 'Charlie Brown',
            'department_id' => $dept2->id,
        ]);
        $responsePending = $this->actingAs($this->admin)->get(route('attendances.index', ['date' => '2026-09-11', 'status' => 'Pending']));
        $responsePending->assertSee($emp3->name);
        $responsePending->assertDontSee($this->employee->name);
        $responsePending->assertDontSee($emp2->name);
    }

    public function test_attendance_searching_by_employee_id_or_name(): void
    {
        $emp2 = Employee::factory()->create([
            'employee_id' => 'EMP-8888',
            'name' => 'Robert Johnson',
            'department_id' => $this->department->id,
        ]);

        $responseSearch = $this->actingAs($this->admin)->get(route('attendances.index', ['date' => '2026-09-11', 'search' => 'EMP-8888']));
        $responseSearch->assertSee('Robert Johnson');
        $responseSearch->assertDontSee('John Doe');
    }

    public function test_employee_attendance_history_view_and_summary_calculations(): void
    {
        // 2 Present, 1 Half Day, 1 Absent, 1 Leave = 5 records in Sep 2026
        Attendance::create([
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-01',
            'status' => 'Present',
            'check_in' => '09:00:00',
            'check_out' => '17:00:00',
        ]);
        Attendance::create([
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-02',
            'status' => 'Present',
            'check_in' => '09:00:00',
            'check_out' => '17:00:00',
        ]);
        Attendance::create([
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-03',
            'status' => 'Half Day',
            'check_in' => '09:00:00',
            'check_out' => '13:00:00',
        ]);
        Attendance::create([
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-04',
            'status' => 'Absent',
        ]);
        Attendance::create([
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-05',
            'status' => 'Leave',
        ]);

        $response = $this->actingAs($this->admin)->get(route('employees.attendance', [
            'employee' => $this->employee,
            'month' => '2026-09',
        ]));

        $response->assertOk();
        $response->assertSee('Attendance History');
        $response->assertSee($this->employee->name);
        // Formula: (Present: 2 + Half Day: 1 * 0.5) / (Present: 2 + Half Day: 1 + Absent: 1) = 2.5 / 4 * 100 = 62.5%
        $response->assertSee('62.5%');
        // 12-hour formatted time
        $response->assertSee('09:00 AM');
        $response->assertSee('05:00 PM');
    }

    public function test_employee_with_attendance_cannot_be_deleted(): void
    {
        Attendance::create([
            'employee_id' => $this->employee->id,
            'attendance_date' => '2026-09-11',
            'status' => 'Present',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('employees.destroy', $this->employee));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('employees', ['id' => $this->employee->id]);
    }
}

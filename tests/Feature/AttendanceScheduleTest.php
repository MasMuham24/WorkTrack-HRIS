<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AttendanceScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function office(array $overrides = []): Office
    {
        return Office::create(array_merge([
            'name' => 'Kantor Pusat',
            'latitude' => 0,
            'longitude' => 0,
            'radius' => 100,
            'check_in_time' => '08:00:00',
            'check_out_time' => '16:00:00',
        ], $overrides));
    }

    private function employee(): User
    {
        return User::factory()->create(['role' => 'employee']);
    }

    private function hr(): User
    {
        return User::factory()->create(['role' => 'hr']);
    }

    private function checkIn(
        User $user,
        string $route = 'employee.attendance.check-in',
        float $latitude = 0,
        float $longitude = 0
    ): TestResponse {
        return $this->actingAs($user)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->postJson(route($route), [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'accuracy' => 5,
            ]);
    }

    private function checkOut(
        User $user,
        string $route = 'employee.attendance.check-out',
        float $latitude = 0,
        float $longitude = 0
    ): TestResponse {
        return $this->actingAs($user)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->postJson(route($route), [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'accuracy' => 5,
            ]);
    }

    public function test_check_in_on_time_is_present(): void
    {
        $this->office();
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:00:00'));

        $this->checkIn($this->employee())->assertOk();

        $this->assertDatabaseHas('attendances', [
            'status' => 'hadir',
            'late_minutes' => 0,
            'check_in' => '08:00:00',
        ]);
    }

    public function test_check_in_inside_five_minute_tolerance_is_present(): void
    {
        $this->office();
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:04:00'));

        $this->checkIn($this->employee())->assertOk();

        $this->assertDatabaseHas('attendances', [
            'status' => 'hadir',
            'late_minutes' => 0,
        ]);
    }

    public function test_check_in_exactly_at_tolerance_boundary_is_present(): void
    {
        $this->office();
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:05:00'));

        $this->checkIn($this->employee())->assertOk();

        $this->assertDatabaseHas('attendances', [
            'status' => 'hadir',
            'late_minutes' => 0,
        ]);
    }

    public function test_check_in_after_tolerance_is_late_and_minutes_use_scheduled_time(): void
    {
        $this->office();
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:06:00'));

        $this->checkIn($this->employee())->assertOk();

        $this->assertDatabaseHas('attendances', [
            'status' => 'terlambat',
            'late_minutes' => 6,
        ]);
    }

    public function test_each_office_applies_its_own_check_in_time(): void
    {
        $this->office([
            'name' => 'Kantor Cabang',
            'check_in_time' => '09:00:00',
            'check_out_time' => '17:00:00',
        ]);
        Carbon::setTestNow(Carbon::parse('2026-10-29 09:06:00'));

        $this->checkIn($this->employee())->assertOk();

        $this->assertDatabaseHas('attendances', [
            'status' => 'terlambat',
            'late_minutes' => 6,
        ]);
    }

    public function test_later_office_schedule_still_allows_on_time_check_in(): void
    {
        $this->office([
            'name' => 'Kantor Cabang',
            'check_in_time' => '09:00:00',
            'check_out_time' => '17:00:00',
        ]);
        Carbon::setTestNow(Carbon::parse('2026-10-29 09:04:00'));

        $this->checkIn($this->employee())->assertOk();

        $this->assertDatabaseHas('attendances', [
            'status' => 'hadir',
            'late_minutes' => 0,
        ]);
    }

    public function test_check_in_outside_radius_is_rejected(): void
    {
        $this->office(['latitude' => 10, 'longitude' => 10, 'radius' => 100]);
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:00:00'));

        $response = $this->checkIn($this->employee());

        $response->assertStatus(422);
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_double_check_in_is_rejected(): void
    {
        $this->office();
        $employee = $this->employee();
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:00:00'));

        $this->checkIn($employee)->assertOk();
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:01:00'));

        $this->checkIn($employee)->assertStatus(422);
        $this->assertDatabaseCount('attendances', 1);
    }

    public function test_check_out_still_works(): void
    {
        $this->office();
        $employee = $this->employee();
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:00:00'));

        $this->checkIn($employee)->assertOk();
        Carbon::setTestNow(Carbon::parse('2026-10-29 16:30:00'));

        $response = $this->actingAs($employee)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->postJson(route('employee.attendance.check-out'), [
                'latitude' => 0,
                'longitude' => 0,
                'accuracy' => 5,
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('attendances', [
            'user_id' => $employee->id,
            'check_out' => '16:30:00',
        ]);
    }

    public function test_check_out_before_scheduled_time_is_rejected(): void
    {
        $this->office();
        $employee = $this->employee();
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:00:00'));

        $this->checkIn($employee)->assertOk();
        Carbon::setTestNow(Carbon::parse('2026-10-29 15:59:00'));

        $response = $this->checkOut($employee);

        $response->assertStatus(422);
        $this->assertStringContainsString('16:00', $response->json('message'));
        $this->assertDatabaseHas('attendances', [
            'user_id' => $employee->id,
            'check_out' => null,
        ]);
    }

    public function test_check_out_exactly_at_scheduled_time_is_allowed(): void
    {
        $this->office();
        $employee = $this->employee();
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:00:00'));

        $this->checkIn($employee)->assertOk();
        Carbon::setTestNow(Carbon::parse('2026-10-29 16:00:00'));

        $this->checkOut($employee)->assertOk();

        $this->assertDatabaseHas('attendances', [
            'user_id' => $employee->id,
            'check_out' => '16:00:00',
        ]);
    }

    public function test_different_offices_use_their_own_check_out_times(): void
    {
        $this->office();
        $this->office([
            'name' => 'Kantor Cabang',
            'latitude' => 10,
            'longitude' => 10,
            'check_out_time' => '17:00:00',
        ]);

        $firstEmployee = $this->employee();
        $secondEmployee = $this->employee();

        Carbon::setTestNow(Carbon::parse('2026-10-29 08:00:00'));
        $this->checkIn($firstEmployee)->assertOk();
        $this->checkIn($secondEmployee, 'employee.attendance.check-in', 10, 10)->assertOk();

        Carbon::setTestNow(Carbon::parse('2026-10-29 16:30:00'));

        $this->checkOut($firstEmployee)->assertOk();
        $response = $this->checkOut($secondEmployee, 'employee.attendance.check-out', 10, 10);

        $response->assertStatus(422);
        $this->assertStringContainsString('17:00', $response->json('message'));
    }

    public function test_check_out_without_check_in_is_rejected(): void
    {
        $this->office();
        Carbon::setTestNow(Carbon::parse('2026-10-29 17:00:00'));

        $response = $this->checkOut($this->employee());

        $response->assertStatus(422);
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_duplicate_check_out_is_rejected(): void
    {
        $this->office();
        $employee = $this->employee();
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:00:00'));

        $this->checkIn($employee)->assertOk();
        Carbon::setTestNow(Carbon::parse('2026-10-29 16:00:00'));
        $this->checkOut($employee)->assertOk();
        Carbon::setTestNow(Carbon::parse('2026-10-29 16:05:00'));

        $this->checkOut($employee)->assertStatus(422);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $employee->id,
            'check_out' => '16:00:00',
        ]);
        $this->assertDatabaseCount('attendances', 1);
    }

    public function test_hr_check_out_follows_the_same_schedule(): void
    {
        $this->office();
        $hr = $this->hr();
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:00:00'));

        $this->checkIn($hr, 'hr.attendance.check-in')->assertOk();

        Carbon::setTestNow(Carbon::parse('2026-10-29 15:59:00'));
        $this->checkOut($hr, 'hr.attendance.check-out')->assertStatus(422);

        Carbon::setTestNow(Carbon::parse('2026-10-29 16:00:00'));
        $this->checkOut($hr, 'hr.attendance.check-out')->assertOk();

        $this->assertDatabaseHas('attendances', [
            'user_id' => $hr->id,
            'check_out' => '16:00:00',
        ]);
    }

    public function test_employee_button_stays_disabled_before_check_out_time(): void
    {
        $this->office();
        $employee = $this->employee();
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:00:00'));

        $this->checkIn($employee)->assertOk();
        Carbon::setTestNow(Carbon::parse('2026-10-29 15:59:00'));

        $response = $this->actingAs($employee)->get(route('employee.attendance.index'));

        $response->assertOk()
            ->assertSee('data-checkout-enabled="false"', false)
            ->assertSee('data-checkout-label="16:00"', false)
            ->assertSee('Check out dapat dilakukan mulai pukul 16:00 WIB.');
    }

    public function test_employee_button_is_enabled_at_check_out_time(): void
    {
        $this->office();
        $employee = $this->employee();
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:00:00'));

        $this->checkIn($employee)->assertOk();
        Carbon::setTestNow(Carbon::parse('2026-10-29 16:00:00'));

        $response = $this->actingAs($employee)->get(route('employee.attendance.index'));

        $response->assertOk()
            ->assertSee('data-checkout-enabled="true"', false)
            ->assertSee('Check out sudah dapat dilakukan.');
    }

    public function test_hr_button_stays_disabled_before_check_out_time(): void
    {
        $this->office();
        $hr = $this->hr();
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:00:00'));

        $this->checkIn($hr, 'hr.attendance.check-in')->assertOk();
        Carbon::setTestNow(Carbon::parse('2026-10-29 15:59:00'));

        $response = $this->actingAs($hr)->get(route('hr.attendance.index'));

        $response->assertOk()
            ->assertSee('data-checkout-enabled="false"', false)
            ->assertSee('Check out dapat dilakukan mulai pukul 16:00 WIB.');
    }

    public function test_hr_and_employee_use_the_same_lateness_rule(): void
    {
        $this->office();
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:06:00'));

        $this->checkIn($this->hr(), 'hr.attendance.check-in')->assertOk();

        $this->assertDatabaseHas('attendances', [
            'status' => 'terlambat',
            'late_minutes' => 6,
        ]);
    }

    public function test_schedule_change_does_not_alter_historical_attendance(): void
    {
        $office = $this->office();
        $employee = $this->employee();

        $historical = Attendance::create([
            'user_id' => $employee->id,
            'office_id' => $office->id,
            'attendance_date' => '2026-10-28',
            'check_in' => '08:10:00',
            'check_out' => '16:00:00',
            'status' => 'terlambat',
            'late_minutes' => 10,
        ]);

        $office->update([
            'check_in_time' => '10:00:00',
            'check_out_time' => '18:00:00',
        ]);

        $historical->refresh();

        $this->assertSame('terlambat', $historical->status);
        $this->assertSame(10, (int) $historical->late_minutes);
        $this->assertSame('08:10', $historical->check_in->format('H:i'));
    }
}

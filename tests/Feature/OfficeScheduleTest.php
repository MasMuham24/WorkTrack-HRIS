<?php

namespace Tests\Feature;

use App\Models\Office;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficeScheduleTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function officePayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Kantor Pusat',
            'latitude' => -6.1947,
            'longitude' => 106.8241,
            'radius' => 100,
            'check_in_time' => '08:00',
            'check_out_time' => '16:00',
        ], $overrides);
    }

    public function test_admin_can_create_office_with_schedule(): void
    {
        $response = $this->actingAs($this->admin())
            ->post(route('management.offices.store'), $this->officePayload([
                'check_in_time' => '08:30',
                'check_out_time' => '17:00',
            ]));

        $response->assertRedirect(route('management.offices.index'));

        $office = Office::query()->where('name', 'Kantor Pusat')->firstOrFail();

        $this->assertSame('08:30', substr($office->check_in_time, 0, 5));
        $this->assertSame('17:00', substr($office->check_out_time, 0, 5));
    }

    public function test_admin_can_update_office_schedule(): void
    {
        $office = Office::create($this->officePayload());

        $response = $this->actingAs($this->admin())
            ->put(route('management.offices.update', $office), $this->officePayload([
                'name' => 'Kantor Cabang',
                'check_in_time' => '09:15',
                'check_out_time' => '18:00',
            ]));

        $response->assertRedirect(route('management.offices.index'));

        $office->refresh();

        $this->assertSame('Kantor Cabang', $office->name);
        $this->assertSame('09:15', substr($office->check_in_time, 0, 5));
        $this->assertSame('18:00', substr($office->check_out_time, 0, 5));
    }

    public function test_edit_form_displays_stored_schedule(): void
    {
        $office = Office::create($this->officePayload([
            'check_in_time' => '08:30',
            'check_out_time' => '17:00',
        ]));

        $response = $this->actingAs($this->admin())
            ->get(route('management.offices.edit', $office));

        $response->assertOk()
            ->assertSee('value="08:30"', false)
            ->assertSee('value="17:00"', false);
    }

    public function test_schedule_is_required_and_must_use_valid_time_format(): void
    {
        $response = $this->actingAs($this->admin())
            ->post(route('management.offices.store'), $this->officePayload([
                'check_in_time' => '25:00',
                'check_out_time' => '16:00',
            ]));

        $response->assertSessionHasErrors('check_in_time');
        $this->assertDatabaseCount('offices', 0);
    }

    public function test_schedule_fields_are_required(): void
    {
        $payload = $this->officePayload();
        unset($payload['check_in_time'], $payload['check_out_time']);

        $response = $this->actingAs($this->admin())
            ->post(route('management.offices.store'), $payload);

        $response->assertSessionHasErrors(['check_in_time', 'check_out_time']);
        $this->assertDatabaseCount('offices', 0);
    }

    public function test_check_out_time_must_be_after_check_in_time(): void
    {
        $response = $this->actingAs($this->admin())
            ->post(route('management.offices.store'), $this->officePayload([
                'check_in_time' => '08:00',
                'check_out_time' => '08:00',
            ]));

        $response->assertSessionHasErrors('check_out_time');
        $this->assertDatabaseCount('offices', 0);
    }

    public function test_check_out_time_earlier_than_check_in_time_is_rejected(): void
    {
        $response = $this->actingAs($this->admin())
            ->post(route('management.offices.store'), $this->officePayload([
                'check_in_time' => '09:00',
                'check_out_time' => '08:59',
            ]));

        $response->assertSessionHasErrors('check_out_time');
        $this->assertDatabaseCount('offices', 0);
    }
}

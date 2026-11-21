<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Office;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionNameUniquenessTest extends TestCase
{
    use RefreshDatabase;
    private User $admin;
    private Office $officeA;
    private Office $officeB;
    private Departemen $departmentA;
    private Departemen $departmentB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->officeA = $this->createOffice('Kantor A');
        $this->officeB = $this->createOffice('Kantor B');
        $this->departmentA = $this->createDepartment('Departemen A', $this->officeA->id);
        $this->departmentB = $this->createDepartment('Departemen B', $this->officeB->id);
        $this->actingAs($this->admin);
    }

    private function createOffice(string $name): Office
    {
        return Office::create([
            'name' => $name,
            'latitude' => -6.895055,
            'longitude' => 110.617490,
            'radius' => 50,
            'check_in_time' => '08:00:00',
            'check_out_time' => '16:00:00',
        ]);
    }

    private function createDepartment(string $name, int $officeId
    ): Departemen {
        return Departemen::create([
            'name' => $name,
            'office_id' => $officeId,
        ]);
    }

    private function globalPayload(string $name): array
    {
        return [
            'name' => $name,
            'position_type' => 'global',
            'departement_ids' => [$this->departmentA->id],
        ];
    }

    private function officePayload(string $name, Office $office, Departemen $department
    ): array {
        return [
            'name' => $name,
            'position_type' => 'office_specific',
            'office_id' => $office->id,
            'departement_ids' => [$department->id],
        ];
    }

    public function test_global_position_name_must_be_unique(): void
    {
        Position::create([
            'name' => 'Manager',
            'position_type' => 'global',
            'office_id' => null,
        ]);

        $response = $this->post(
            route('management.positions.store'),
            $this->globalPayload('Manager')
        );
        $response->assertSessionHasErrors('name');
    }

    public function test_office_specific_position_name_must_be_unique_in_same_office(): void
    {
        Position::create([
            'name' => 'Supervisor',
            'position_type' => 'office_specific',
            'office_id' => $this->officeA->id,
        ]);
        $response = $this->post(route('management.positions.store'), $this->officePayload('Supervisor',
            $this->officeA,
            $this->departmentA
        )
        );
        $response->assertSessionHasErrors('name');
    }

    public function test_same_office_specific_name_is_allowed_in_different_offices(): void
    {
        Position::create([
            'name' => 'Supervisor',
            'position_type' => 'office_specific',
            'office_id' => $this->officeA->id,
        ]);

        $response = $this->post(route('management.positions.store'), $this->officePayload(
            'Supervisor',
            $this->officeB,
            $this->departmentB
        )
        );

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('positions', [
            'name' => 'Supervisor',
            'position_type' => 'office_specific',
            'office_id' => $this->officeB->id,
        ]);
    }

    public function test_same_name_is_allowed_between_global_and_office_specific_positions(): void
    {
        Position::create([
            'name' => 'Manager',
            'position_type' => 'global',
            'office_id' => null,
        ]);

        $response = $this->post(route('management.positions.store'), $this->officePayload('Manager',
            $this->officeA,
            $this->departmentA
        )
        );
        $response->assertSessionHasNoErrors();
    }

    public function test_updating_position_without_changing_its_name_is_allowed(): void
    {
        $position = Position::create([
            'name' => 'Manager',
            'position_type' => 'global',
            'office_id' => null,
        ]);
        $response = $this->put(
            route('management.positions.update', $position),
            $this->globalPayload('Manager')
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('positions', [
            'id' => $position->id,
            'name' => 'Manager',
        ]);
    }
}

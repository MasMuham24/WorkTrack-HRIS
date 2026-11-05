<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Office;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DepartmentOfficeScopeTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function createOffice(array $attributes = []): Office
    {
        return Office::create(array_merge([
            'name' => 'Kantor '.fake()->city(),
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius' => 100,
            'check_in_time' => '08:00:00',
            'check_out_time' => '17:00:00',
        ], $attributes));
    }

    /**
     * 1. Nama departemen sama dapat dibuat pada dua office berbeda.
     */
    public function test_same_department_name_can_be_created_in_two_different_offices(): void
    {
        $officeA = $this->createOffice(['name' => 'Kantor Jakarta']);
        $officeB = $this->createOffice(['name' => 'Kantor Surabaya']);

        $responseA = $this->actingAs($this->admin())->post(route('management.departments.store'), [
            'office_id' => $officeA->id,
            'name' => 'Information Technology',
            'description' => 'IT Jakarta',
        ]);
        $responseA->assertRedirect(route('management.departments.index'));

        $responseB = $this->actingAs($this->admin())->post(route('management.departments.store'), [
            'office_id' => $officeB->id,
            'name' => 'Information Technology',
            'description' => 'IT Surabaya',
        ]);
        $responseB->assertRedirect(route('management.departments.index'));

        $this->assertDatabaseHas('departemens', [
            'office_id' => $officeA->id,
            'name' => 'Information Technology',
        ]);

        $this->assertDatabaseHas('departemens', [
            'office_id' => $officeB->id,
            'name' => 'Information Technology',
        ]);
    }

    /**
     * 2. Nama departemen duplikat pada office yang sama ditolak (FormRequest).
     */
    public function test_duplicate_department_name_in_same_office_is_rejected_by_validation(): void
    {
        $office = $this->createOffice(['name' => 'Kantor Utama']);

        Departemen::create([
            'office_id' => $office->id,
            'name' => 'Human Resource',
            'description' => 'HRD',
        ]);

        $response = $this->actingAs($this->admin())->post(route('management.departments.store'), [
            'office_id' => $office->id,
            'name' => 'Human Resource',
            'description' => 'Duplicate HRD',
        ]);

        $response->assertSessionHasErrors(['name']);
        $this->assertCount(1, Departemen::where('office_id', $office->id)->where('name', 'Human Resource')->get());
    }

    /**
     * 3. Constraint database menolak duplikasi meskipun validasi request dilewati.
     */
    public function test_database_constraint_rejects_duplicate_in_same_office(): void
    {
        $office = $this->createOffice(['name' => 'Kantor Utama']);

        DB::table('departemens')->insert([
            'office_id' => $office->id,
            'name' => 'Finance',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('departemens')->insert([
            'office_id' => $office->id,
            'name' => 'Finance',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * 4. Departemen tanpa office ditolak.
     */
    public function test_department_without_office_is_rejected(): void
    {
        $response = $this->actingAs($this->admin())->post(route('management.departments.store'), [
            'name' => 'Marketing',
            'description' => 'Tanpa Office',
        ]);

        $response->assertSessionHasErrors(['office_id']);

        $office = $this->createOffice();
        // Direct DB insert dengan office_id null harus gagal karena NOT NULL
        $this->expectException(QueryException::class);
        DB::table('departemens')->insert([
            'office_id' => null,
            'name' => 'Marketing',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * 5. Data lima departemen lama tidak berubah.
     */
    public function test_legacy_departments_data_remain_intact(): void
    {
        $office = $this->createOffice(['id' => 1, 'name' => 'PT SUKA MAJU SEJAHTERA']);

        $names = ['IT', 'HR', 'Finance', 'Marketing', 'Produksi'];
        foreach ($names as $name) {
            Departemen::create([
                'office_id' => $office->id,
                'name' => $name,
                'description' => "Divisi {$name}",
            ]);
        }

        $departemens = Departemen::where('office_id', 1)->get();
        $this->assertCount(5, $departemens);
        foreach ($departemens as $dept) {
            $this->assertSame(1, (int) $dept->office_id);
            $this->assertSame('PT SUKA MAJU SEJAHTERA', $dept->office->name);
        }
    }

    /**
     * 6. Foreign key office tetap berfungsi menolak office tidak valid.
     */
    public function test_office_foreign_key_rejects_invalid_office(): void
    {
        $this->expectException(QueryException::class);

        DB::table('departemens')->insert([
            'office_id' => 9999,
            'name' => 'R&D',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * 7. Penghapusan office yang masih memiliki departemen ditolak (restrictOnDelete).
     */
    public function test_deleting_office_with_departments_is_restricted(): void
    {
        $office = $this->createOffice(['name' => 'Kantor Cabang']);

        Departemen::create([
            'office_id' => $office->id,
            'name' => 'Logistik',
        ]);

        $this->expectException(QueryException::class);
        $office->delete();
    }

    /**
     * 8. Update nama departemen tidak mengubah office_id.
     */
    public function test_updating_department_does_not_change_office_id(): void
    {
        $officeA = $this->createOffice(['name' => 'Kantor Asli']);
        $officeB = $this->createOffice(['name' => 'Kantor Lain']);

        $dept = Departemen::create([
            'office_id' => $officeA->id,
            'name' => 'General Affair',
            'description' => 'GA lama',
        ]);

        // Coba kirim office_id kantor lain pada request update
        $response = $this->actingAs($this->admin())->put(
            route('management.departments.update', ['departemen' => $dept->id]),
            [
                'office_id' => $officeB->id,
                'name' => 'General Affairs Updated',
                'description' => 'GA Baru',
            ]
        );

        $response->assertRedirect(route('management.departments.index'));

        $dept->refresh();
        $this->assertSame('General Affairs Updated', $dept->name);
        $this->assertSame('GA Baru', $dept->description);
        $this->assertSame($officeA->id, $dept->office_id); // office_id tidak berubah
    }

    /**
     * 9. Rollback menolak kondisi nama global duplikat.
     */
    public function test_rollback_aborts_safely_if_cross_office_duplicates_exist(): void
    {
        $officeA = $this->createOffice(['name' => 'Office A']);
        $officeB = $this->createOffice(['name' => 'Office B']);

        Departemen::create(['office_id' => $officeA->id, 'name' => 'Sales']);
        Departemen::create(['office_id' => $officeB->id, 'name' => 'Sales']);

        $migration = require database_path('migrations/2026_10_31_100000_update_departemens_unique_scoped_to_office.php');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Rollback dibatalkan: Terdapat nama departemen yang sama di kantor berbeda');

        $migration->validateRollbackPreconditions();
    }

    /**
     * 10. Tidak ada regresi pada CRUD departemen lama.
     */
    public function test_no_regression_on_legacy_department_crud(): void
    {
        $office = $this->createOffice(['name' => 'Kantor Pusat']);

        // Index
        $response = $this->actingAs($this->admin())->get(route('management.departments.index'));
        $response->assertOk();

        // Create view
        $response = $this->actingAs($this->admin())->get(route('management.departments.create'));
        $response->assertOk();
        $response->assertSee($office->name);

        // Store
        $response = $this->actingAs($this->admin())->post(route('management.departments.store'), [
            'office_id' => $office->id,
            'name' => 'Quality Assurance',
            'description' => 'QA Team',
        ]);
        $response->assertRedirect(route('management.departments.index'));

        $dept = Departemen::where('name', 'Quality Assurance')->firstOrFail();

        // Edit view
        $response = $this->actingAs($this->admin())->get(route('management.departments.edit', ['departemen' => $dept->id]));
        $response->assertOk();
        $response->assertSee('Quality Assurance');
        $response->assertSee($office->name);

        // Update
        $response = $this->actingAs($this->admin())->put(route('management.departments.update', ['departemen' => $dept->id]), [
            'name' => 'Quality Control',
            'description' => 'QC Team',
        ]);
        $response->assertRedirect(route('management.departments.index'));

        // Destroy
        $response = $this->actingAs($this->admin())->delete(route('management.departments.destroy', ['departemen' => $dept->id]));
        $response->assertRedirect(route('management.departments.index'));
        $this->assertDatabaseMissing('departemens', ['id' => $dept->id]);
    }
}

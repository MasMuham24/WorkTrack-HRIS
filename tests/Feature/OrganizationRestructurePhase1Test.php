<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Departemen;
use App\Models\Office;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OrganizationRestructurePhase1Test extends TestCase
{
    use RefreshDatabase;

    public function test_schema_has_new_organization_columns_and_pivot_table(): void
    {
        $this->assertTrue(Schema::hasColumn('departemens', 'office_id'));
        $this->assertTrue(Schema::hasColumn('users', 'office_id'));
        $this->assertTrue(Schema::hasColumn('positions', 'office_id'));
        $this->assertTrue(Schema::hasColumn('positions', 'position_type'));
        $this->assertTrue(Schema::hasTable('departement_position'));
        $this->assertTrue(Schema::hasColumns('departement_position', ['id', 'departement_id', 'position_id']));
    }

    public function test_backfill_and_data_integrity_rules(): void
    {
        // 1. Setup seed office ID 1
        $office = Office::create([
            'id' => 1,
            'name' => 'PT SUKA MAJU SEJAHTERA',
            'latitude' => -6.895055,
            'longitude' => 110.617490,
            'radius' => 50,
            'check_in_time' => '08:00:00',
            'check_out_time' => '16:00:00',
        ]);

        // 2. Setup 5 departemen lama (tanpa office_id, disimulasikan nullable sesuai kondisi pra-Phase 2A.1)
        Schema::table('departemens', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->nullable()->change();
        });

        $deptIT = Departemen::create(['name' => 'Information Technology']);
        $deptHR = Departemen::create(['name' => 'Human Resource']);
        $deptFin = Departemen::create(['name' => 'Finance']);
        $deptMkt = Departemen::create(['name' => 'Marketing']);
        $deptPrd = Departemen::create(['name' => 'Produksi']);

        // 3. Setup 8 posisi lama (dengan legacy departement_id, tanpa office_id)
        $pos1 = Position::create(['name' => 'Backend Developer', 'departement_id' => $deptIT->id]);
        $pos2 = Position::create(['name' => 'Frontend Developer', 'departement_id' => $deptIT->id]);
        $pos3 = Position::create(['name' => 'Full Stack Developer', 'departement_id' => $deptIT->id]);
        $pos4 = Position::create(['name' => 'HR Staff', 'departement_id' => $deptHR->id]);
        $pos5 = Position::create(['name' => 'HR Manager', 'departement_id' => $deptHR->id]);
        $pos6 = Position::create(['name' => 'Finance Staff', 'departement_id' => $deptFin->id]);
        $pos7 = Position::create(['name' => 'Marketing Staff', 'departement_id' => $deptMkt->id]);
        $pos8 = Position::create(['name' => 'Operator', 'departement_id' => $deptPrd->id]);

        $originalPassword = 'SecretPassword123!';
        $hashedPassword = Hash::make($originalPassword);

        // 4. Setup 1 admin
        $admin = User::create([
            'employee_code' => 'ADM001',
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => $hashedPassword,
            'departement_id' => $deptIT->id,
            'position_id' => $pos3->id,
        ]);

        // 5. Setup 7 HR
        for ($i = 1; $i <= 7; $i++) {
            User::create([
                'employee_code' => "HR00{$i}",
                'name' => "HR User {$i}",
                'username' => "hr{$i}",
                'email' => "hr{$i}@example.com",
                'role' => 'hr',
                'password' => $hashedPassword,
                'departement_id' => $deptHR->id,
                'position_id' => $pos4->id,
            ]);
        }

        // 6. Setup 23 employee
        for ($i = 1; $i <= 23; $i++) {
            User::create([
                'employee_code' => 'EMP'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'name' => "Employee {$i}",
                'username' => "emp{$i}",
                'email' => "emp{$i}@example.com",
                'role' => 'employee',
                'password' => $hashedPassword,
                'departement_id' => $deptIT->id,
                'position_id' => $pos1->id,
            ]);
        }

        // 7. Setup data absensi lama
        $attendance = Attendance::create([
            'user_id' => $admin->id,
            'office_id' => $office->id,
            'attendance_date' => '2026-10-25',
            'check_in' => '08:00:00',
            'check_out' => '17:00:00',
            'status' => 'hadir',
            'late_minutes' => 0,
        ]);

        // Jalankan migrasi instance backfill logic
        $migration = require database_path('migrations/2026_10_30_160000_restructure_organization_tables_phase_1.php');
        $migration->validatePreconditions();
        $migration->performBackfill();

        // Verifikasi 1: Admin tetap office_id NULL
        $admin->refresh();
        $this->assertNull($admin->office_id);
        $this->assertSame('admin', $admin->role);

        // Verifikasi 2: HR dan employee lama memiliki office_id 1
        $hrs = User::where('role', 'hr')->get();
        $this->assertCount(7, $hrs);
        foreach ($hrs as $hr) {
            $this->assertSame(1, (int) $hr->office_id);
            $this->assertNotNull($hr->office);
            $this->assertSame('PT SUKA MAJU SEJAHTERA', $hr->office->name);
        }

        $employees = User::where('role', 'employee')->get();
        $this->assertCount(23, $employees);
        foreach ($employees as $emp) {
            $this->assertSame(1, (int) $emp->office_id);
            $this->assertNotNull($emp->office);
            $this->assertSame('PT SUKA MAJU SEJAHTERA', $emp->office->name);
        }

        // Verifikasi 3: Semua 5 departemen lama memiliki office_id 1
        $departemens = Departemen::all();
        $this->assertCount(5, $departemens);
        foreach ($departemens as $dept) {
            $this->assertSame(1, (int) $dept->office_id);
            $this->assertNotNull($dept->office);
            $this->assertSame('PT SUKA MAJU SEJAHTERA', $dept->office->name);
        }

        // Verifikasi 4: Semua posisi lama memiliki office_id 1, position_type office_specific, dan terpetakan ke pivot
        $positions = Position::all();
        $this->assertCount(8, $positions);
        foreach ($positions as $pos) {
            $this->assertSame(1, (int) $pos->office_id);
            $this->assertSame('office_specific', $pos->position_type);
            $this->assertTrue($pos->isOfficeSpecific());
            $this->assertFalse($pos->isGlobal());

            // Terpetakan di pivot
            $this->assertDatabaseHas('departement_position', [
                'departement_id' => $pos->departement_id,
                'position_id' => $pos->id,
            ]);

            // Verifikasi relasi pivot Eloquent
            $this->assertTrue($pos->departements->contains('id', $pos->departement_id));
        }

        // Verifikasi 5: Posisi lama tetap dapat diakses melalui relasi legacy
        $pos1->refresh();
        $this->assertNotNull($pos1->departement);
        $this->assertSame('Information Technology', $pos1->departement->name);

        $deptIT->refresh();
        $this->assertTrue($deptIT->positions->contains('id', $pos1->id));
        $this->assertTrue($deptIT->positionsViaPivot->contains('id', $pos1->id));

        // Verifikasi 6: Migrasi tidak mengubah password, ID, atau data absensi lama
        $this->assertTrue(Hash::check($originalPassword, $admin->password));
        $this->assertSame('admin@example.com', $admin->email);
        $this->assertSame('ADM001', $admin->employee_code);

        $attendance->refresh();
        $this->assertSame(1, (int) $attendance->office_id);
        $this->assertSame('hadir', $attendance->status);
        $this->assertSame('2026-10-25', $attendance->attendance_date->format('Y-m-d'));
    }

    public function test_migration_safety_checks_abort_if_preconditions_not_met(): void
    {
        $migration = require database_path('migrations/2026_10_30_160000_restructure_organization_tables_phase_1.php');

        // Skenario: Office 1 tidak ada
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Office ID 1 tidak ditemukan');

        $migration->validatePreconditions();
    }
}

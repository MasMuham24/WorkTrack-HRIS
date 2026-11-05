<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Additive schema modifications
        Schema::table('departemens', function (Blueprint $table) {
            $table->foreignId('office_id')
                ->nullable()
                ->after('id')
                ->constrained('offices')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('office_id')
                ->nullable()
                ->after('id')
                ->constrained('offices')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->foreignId('office_id')
                ->nullable()
                ->after('id')
                ->constrained('offices')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->enum('position_type', ['global', 'office_specific'])
                ->default('office_specific')
                ->after('name');
        });

        Schema::create('departement_position', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departement_id')
                ->constrained('departemens')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('position_id')
                ->constrained('positions')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->unique(['departement_id', 'position_id']);
            $table->timestamps();
        });

        // Tambah role hr_manager pada enum users.role jika MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'hr_manager', 'hr', 'employee') NOT NULL DEFAULT 'employee'");
        }

        // 2. Pre-condition checks & Backfill data
        $hasData = DB::table('users')->exists() || DB::table('departemens')->exists() || DB::table('positions')->exists();

        if ($hasData) {
            $this->validatePreconditions();
            $this->performBackfill();
        }
    }

    /**
     * Validasi prasyarat keamanan data sebelum backfill.
     */
    public function validatePreconditions(): void
    {
        // a. Periksa keberadaan office ID 1
        if (! DB::table('offices')->where('id', 1)->exists()) {
            throw new RuntimeException('Migrasi dibatalkan: Office ID 1 tidak ditemukan.');
        }

        // b. Pastikan jumlah admin tepat satu
        $adminCount = DB::table('users')->where('role', 'admin')->count();
        if ($adminCount !== 1) {
            throw new RuntimeException("Migrasi dibatalkan: Jumlah admin tidak tepat 1 (ditemukan: {$adminCount}).");
        }

        // c. Pastikan jumlah HR lama tujuh dan employee lama 23
        $hrCount = DB::table('users')->where('role', 'hr')->count();
        if ($hrCount !== 7) {
            throw new RuntimeException("Migrasi dibatalkan: Jumlah HR lama tidak tepat 7 (ditemukan: {$hrCount}).");
        }

        $employeeCount = DB::table('users')->where('role', 'employee')->count();
        if ($employeeCount !== 23) {
            throw new RuntimeException("Migrasi dibatalkan: Jumlah employee lama tidak tepat 23 (ditemukan: {$employeeCount}).");
        }

        // d. Pastikan semua posisi memiliki departement_id valid
        $deptIds = DB::table('departemens')->pluck('id')->all();
        $invalidPositions = DB::table('positions')->whereNotIn('departement_id', $deptIds)->count();
        if ($invalidPositions > 0) {
            throw new RuntimeException("Migrasi dibatalkan: Ditemukan {$invalidPositions} posisi dengan departement_id tidak valid.");
        }
    }

    /**
     * Eksekusi backfill data organisasi lama.
     */
    public function performBackfill(): void
    {
        DB::transaction(function () {
            // departemens.office_id = 1 untuk lima departemen lama
            DB::table('departemens')->whereNull('office_id')->update(['office_id' => 1]);

            // users.office_id = 1 untuk role hr dan employee
            DB::table('users')->whereIn('role', ['hr', 'employee'])->whereNull('office_id')->update(['office_id' => 1]);

            // users.office_id tetap NULL untuk admin
            DB::table('users')->where('role', 'admin')->update(['office_id' => null]);

            // positions.office_id = 1 dan position_type = office_specific untuk delapan posisi lama
            DB::table('positions')->whereNull('office_id')->update([
                'office_id' => 1,
                'position_type' => 'office_specific',
            ]);

            // salin seluruh pasangan departement_id dan position_id dari positions lama ke pivot
            $positions = DB::table('positions')->get(['id', 'departement_id']);
            foreach ($positions as $pos) {
                if ($pos->departement_id) {
                    $exists = DB::table('departement_position')
                        ->where('departement_id', $pos->departement_id)
                        ->where('position_id', $pos->id)
                        ->exists();

                    if (! $exists) {
                        DB::table('departement_position')->insert([
                            'departement_id' => $pos->departement_id,
                            'position_id' => $pos->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departement_position');

        Schema::table('positions', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['office_id']);
            }
            $table->dropColumn(['office_id', 'position_type']);
        });

        Schema::table('users', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['office_id']);
            }
            $table->dropColumn(['office_id']);
        });

        Schema::table('departemens', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['office_id']);
            }
            $table->dropColumn(['office_id']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'hr', 'employee') NOT NULL DEFAULT 'employee'");
        }
    }
};

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
        // 1. Validasi pre-kondisi data sebelum DDL
        $this->validatePreconditions();

        // 2. Modifikasi skema
        Schema::table('departemens', function (Blueprint $table) {
            // Hapus unique index global lama
            $table->dropUnique('departemens_name_unique');

            // Tambahkan unique composite index (office_id, name)
            $table->unique(['office_id', 'name'], 'departemens_office_id_name_unique');

            // Ubah office_id menjadi NOT NULL
            $table->unsignedBigInteger('office_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Validasi pre-kondisi rollback
        $this->validateRollbackPreconditions();

        // 2. Rollback skema
        Schema::table('departemens', function (Blueprint $table) {
            // Hapus composite unique index
            $table->dropUnique('departemens_office_id_name_unique');

            // Kembalikan unique index global lama
            $table->unique('name', 'departemens_name_unique');

            // Kembalikan office_id menjadi nullable
            $table->unsignedBigInteger('office_id')->nullable()->change();
        });
    }

    /**
     * Validasi keamanan data sebelum migrasi up.
     */
    public function validatePreconditions(): void
    {
        // Pastikan tidak ada departemen dengan office_id bernilai NULL
        $nullOfficeCount = DB::table('departemens')->whereNull('office_id')->count();
        if ($nullOfficeCount > 0) {
            throw new RuntimeException("Migrasi dibatalkan: Ditemukan {$nullOfficeCount} departemen dengan office_id bernilai NULL.");
        }

        // Pastikan tidak ada duplikasi nama dalam kantor yang sama
        $duplicates = DB::table('departemens')
            ->select('office_id', 'name', DB::raw('count(*) as total'))
            ->groupBy('office_id', 'name')
            ->having('total', '>', 1)
            ->get();

        if ($duplicates->isNotEmpty()) {
            throw new RuntimeException('Migrasi dibatalkan: Ditemukan duplikasi nama departemen dalam kantor yang sama.');
        }
    }

    /**
     * Validasi keamanan data sebelum rollback.
     */
    public function validateRollbackPreconditions(): void
    {
        // Periksa apakah terdapat nama departemen duplikat lintas kantor
        $globalDuplicates = DB::table('departemens')
            ->select('name', DB::raw('count(*) as total'))
            ->groupBy('name')
            ->having('total', '>', 1)
            ->get();

        if ($globalDuplicates->isNotEmpty()) {
            $duplicateNames = $globalDuplicates->pluck('name')->implode(', ');
            throw new RuntimeException(
                "Rollback dibatalkan: Terdapat nama departemen yang sama di kantor berbeda ({$duplicateNames}). ".
                'Rollback ke unique index global akan menyebabkan pelanggaran integritas data.'
            );
        }
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// use RuntimeException;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('positions')->select('name')->groupBy('name')->havingRaw('COUNT(*) > 1')->exists();
        if ($duplicates) {
            throw new RuntimeException(
                'Migration dibatalkan: ditemukan nama posisi duplikat.'
            );
        }
        Schema::table('positions', function (Blueprint $table) {
            $table->index(
                'departement_id',
                'positions_departement_id_index'
            );
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->dropUnique(
                'positions_departement_id_name_unique'
            );
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->unsignedBigInteger('departement_id')
                ->nullable()
                ->change();

            $table->unique('name', 'positions_name_unique');
        });
    }

    public function down(): void
    {
        $hasNullDepartement = DB::table('positions')->whereNull('departement_id')->exists();
        if ($hasNullDepartement) {
            throw new RuntimeException(
                'Rollback dibatalkan: terdapat posisi tanpa departemen.'
            );
        }
        Schema::table('positions', function (Blueprint $table) {
            $table->dropUnique('positions_name_unique');
        });
        Schema::table('positions', function (Blueprint $table) {
            $table->unsignedBigInteger('departement_id')->nullable(false)->change();
            $table->unique(
                ['departement_id', 'name'],
                'positions_departement_id_name_unique'
            );
        });
    }
};

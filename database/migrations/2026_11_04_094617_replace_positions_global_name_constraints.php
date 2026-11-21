<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $invalidPositions = DB::table('positions')->where(function ($query) {
            $query->where(function ($query) {
                $query->where('position_type', 'global')->whereNotNull('office_id');
            })->orWhere(function ($query) {
                $query->where('position_type', 'office_specific')->whereNull('office_id');
            });
        })->exists();
        if ($invalidPositions) {
            throw new RuntimeException(
                'Migration dibatalkan: ditemukan posisi dengan office_id tidak konsisten.'
            );
        }
        $driver = DB::getDriverName();
        if (! Schema::hasColumn('positions', 'global_name')) {
            if ($driver === 'mysql') {
                DB::statement("
                    ALTER TABLE positions
                    ADD COLUMN global_name VARCHAR(100)
                    GENERATED ALWAYS AS (
                        CASE
                            WHEN position_type = 'global' THEN name
                            ELSE NULL
                        END
                    ) STORED
                ");
            } else {
                Schema::table('positions', function ($table) {
                    $table->string('global_name', 100)->nullable()->storedAs("CASE WHEN position_type = 'global' THEN name ELSE NULL END");
                });
            }
        }
        if (! Schema::hasColumn('positions', 'office_specific_name')) {
            if ($driver === 'mysql') {
                DB::statement("
                    ALTER TABLE positions
                    ADD COLUMN office_specific_name VARCHAR(100)
                    GENERATED ALWAYS AS (
                        CASE
                            WHEN position_type = 'office_specific' THEN name
                            ELSE NULL
                        END
                    ) STORED
                ");
            } else {
                Schema::table('positions', function ($table) {
                    $table->string('office_specific_name', 100)->nullable()->storedAs("CASE WHEN position_type = 'office_specific' THEN name ELSE NULL END");
                });
            }
        }
        Schema::table('positions', function ($table) {
            if (! Schema::hasIndex('positions', 'positions_global_name_unique')) {
                $table->unique('global_name','positions_global_name_unique');
            }
            if (! Schema::hasIndex('positions', 'positions_office_specific_name_unique')) {
                $table->unique(['office_id', 'office_specific_name'], 'positions_office_specific_name_unique');
            }
            if (Schema::hasIndex('positions', 'positions_name_unique')) {
                $table->dropUnique('positions_name_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasDuplicateNames = DB::table('positions')->select('name')->groupBy('name')->havingRaw('COUNT(*) > 1')->exists();
        if ($hasDuplicateNames) {
            throw new RuntimeException(
                'Rollback dibatalkan: terdapat nama posisi duplikat. '
                .'Unique global tidak dapat dipulihkan.'
            );
        }

        Schema::table('positions', function ($table) {
            if (Schema::hasIndex('positions', 'positions_global_name_unique')) {
                $table->dropUnique('positions_global_name_unique');
            }

            if (Schema::hasIndex('positions', 'positions_office_specific_name_unique')) {
                $table->dropUnique('positions_office_specific_name_unique');
            }
        });

        Schema::table('positions', function ($table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('positions', 'global_name')) {
                $columnsToDrop[] = 'global_name';
            }
            if (Schema::hasColumn('positions', 'office_specific_name')) {
                $columnsToDrop[] = 'office_specific_name';
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }

            if (! Schema::hasIndex('positions', 'positions_name_unique')) {
                $table->unique('name', 'positions_name_unique');
            }
        });
    }
};

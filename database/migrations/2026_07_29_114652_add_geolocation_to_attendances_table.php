<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {

            $table->foreignId('office_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->decimal('latitude', 10, 7)->nullable()->after('check_out');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->decimal('accuracy', 8, 2)->nullable()->after('longitude');
            $table->decimal('distance', 8, 2)->nullable()->after('accuracy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['office_id']);
            $table->dropColumn([
                'office_id',
                'latitude',
                'longitude',
                'accuracy',
                'distance',
            ]);
        });
    }
};

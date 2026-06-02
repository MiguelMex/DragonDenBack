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
        // 1. Modify works table: set null on delete for age_restriction
        Schema::table('works', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['age_restriction']);

            // Re-add with ON DELETE SET NULL
            $table->foreign('age_restriction')
                  ->references('age_restriction_id')
                  ->on('age_restrictions')
                  ->onDelete('set null');
        });

        // 2. Modify genre_works pivot table: cascade delete on both foreign keys
        Schema::table('genre_works', function (Blueprint $table) {
            // Drop existing foreign keys (if they exist)
            $table->dropForeign(['genre_id']);
            $table->dropForeign(['work_id']);

            // Re-add with ON DELETE CASCADE
            $table->foreign('genre_id')
                  ->references('genre_id')
                  ->on('genres')
                  ->onDelete('cascade');

            $table->foreign('work_id')
                  ->references('work_id')
                  ->on('works')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert works table: restore original foreign key
        Schema::table('works', function (Blueprint $table) {
            $table->dropForeign(['age_restriction']);
            $table->foreign('age_restriction')
                  ->references('age_restriction_id')
                  ->on('age_restrictions');
        });

        // Revert genre_works table: restore foreign keys without cascade
        Schema::table('genre_works', function (Blueprint $table) {
            $table->dropForeign(['genre_id']);
            $table->dropForeign(['work_id']);

            $table->foreign('genre_id')
                  ->references('genre_id')
                  ->on('genres');

            $table->foreign('work_id')
                  ->references('work_id')
                  ->on('works');
        });
    }
};

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
        //
        Schema::table('drafts', function(Blueprint $table){
            $table->dropForeign(['work_in_progress']);

            $table->foreign('work_in_progress')
            ->references('work_in_progress_id')
            ->on('wips')
            ->onDelete('cascade');
        });

        Schema::table('chapters', function (Blueprint $table) {
            // Drop the existing foreign key (if any)
            $table->dropForeign(['draft']);

            // Make the column nullable
            $table->uuid('draft')->nullable()->change();

            // Re‑add the foreign key with ON DELETE SET NULL
            $table->foreign('draft')
                    ->references('draft_id')
                    ->on('drafts')
                    ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('drafts', function(Blueprint $table){
            $table->dropForeign(['work_in_progress']);

            $table->foreign('work_in_progress')
            ->references('work_in_progress_id')
            ->on('wips');
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->dropForeign(['draft']);
            $table->uuid('draft')->nullable(false)->change();
            $table->foreign('draft')
                ->references('draft_id')
                ->on('drafts');
        });
    }
};

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
        Schema::table('works', function(Blueprint $table){
            $table->dropForeign(['work_in_progress']);

            $table->uuid('work_in_progress')->nullable()->change();

            $table->foreign('work_in_progress')->references('work_in_progress_id')->on('wips')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('works', function (Blueprint $table) {
            $table->dropForeign(['work_in_progress']);
            $table->uuid('work_in_progress')->nullable(false)->change();
            $table->foreign('work_in_progress')->references('work_in_progress_id')->on('wips');
        });
    }
};

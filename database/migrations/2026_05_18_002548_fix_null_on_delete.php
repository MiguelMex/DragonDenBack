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
        Schema::table('read_histories', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['chapter']);
            
            // Make the column nullable
            $table->uuid('chapter')->nullable()->change();
            
            // Re-add the foreign key with null on delete
            $table->foreign('chapter')->references('chapter_id')->on('chapters')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('read_histories', function (Blueprint $table) {
            $table->dropForeign(['chapter']);
            $table->uuid('chapter')->nullable(false)->change();
            $table->foreign('chapter')->references('chapter_id')->on('chapters');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations – create final schema.
     */
    public function up(): void
    {
        //0.Profiles table
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('profile_id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users', 'user_id')->cascadeOnDelete();
            $table->string('name',100)->default('Usuario');
            $table->string('nickname',100)->nullable();
            $table->string('description', 2000)->nullable();
            $table->string('avatar', 500)->nullable();
            $table->timestamps();
        });

        // 1. Age restrictions table
        Schema::create('age_restrictions', function (Blueprint $table) {
            $table->uuid('age_restriction_id')->primary();
            $table->string('name', 100)->default('Nueva Restricción');
            $table->string('description', 500);
            $table->timestamps();
        });

        // 2. Works in progress (wips) table
        Schema::create('wips', function (Blueprint $table) {
            $table->uuid('work_in_progress_id')->primary();
            $table->foreignUuid('author')->constrained('profiles', 'profile_id')->cascadeOnDelete();
            $table->string('title', 200)->default('Nuevo Proyecto');
            $table->timestamps();
        });

        // 3. Drafts table
        Schema::create('drafts', function (Blueprint $table) {
            $table->uuid('draft_id')->primary();
            $table->foreignUuid('work_in_progress')->constrained('wips', 'work_in_progress_id')->cascadeOnDelete();
            $table->string('name', 200)->default('Nuevo Borrador');
            $table->string('route',500);
            $table->timestamps();
        });

        // 4. Works table
        Schema::create('works', function (Blueprint $table) {
            $table->uuid('work_id')->primary();
            $table->foreignUuid('author')->constrained('profiles', 'profile_id')->nullOnDelete();
            $table->foreignUuid('age_restriction')->constrained('age_restrictions', 'age_restriction_id')->nullOnDelete();
            $table->string('title', 200)->default('Nuevo Proyecto');
            $table->string('description', 3000)->nullable()->default(null);
            $table->boolean('status')->default(false);
            $table->string('cover',500);
            $table->foreignUuid('work_in_progress')->constrained('wips', 'work_in_progress_id')->nullOnDelete();
            $table->timestamps();
        });

        // 5. Chapters table
        Schema::create('chapters', function (Blueprint $table) {
            $table->uuid('chapter_id')->primary();
            $table->foreignUuid('work')->constrained('works', 'work_id')->cascadeOnDelete();
            $table->string('name', 200)->default('Nuevo capitulo');
            $table->string('cover', 500);
            $table->string('route', 500);
            $table->foreignUuid('draft')->constrained('drafts', 'draft_id')->nullOnDelete();
            $table->timestamps();
        });

        // 6. Genres table
        Schema::create('genres', function (Blueprint $table) {
            $table->uuid('genre_id')->primary();
            $table->string('name', 50)->nullable()->default(null);
            $table->timestamps();
        });

        // 7. Junction table genre_works (first version, singular)
        Schema::create('genre_works', function (Blueprint $table) {
            $table->foreignUuid('genre_id')->constrained('genres', 'genre_id')->cascadeOnDelete();
            $table->foreignUuid('work_id')->constrained('works', 'work_id')->cascadeOnDelete();
            $table->timestamps();
        });

        // 8. Collections table (name added later)
        Schema::create('collections', function (Blueprint $table) {
            $table->uuid('collection_id')->primary();
            $table->string('name', 200);
            $table->text('description')->default('Sin descripción');
            $table->foreignUuid('user')->constrained('profiles', 'profile_id')->nullOnDelete();
            $table->timestamps();
        });

        // 9. Collections – works pivot table
        Schema::create('collections_works', function (Blueprint $table) {
            $table->foreignUuid('collection_id')->constrained('collections', 'collection_id')->cascadeOnDelete();
            $table->foreignUuid('work_id')->constrained('works', 'work_id')->cascadeOnDelete();
            $table->timestamps();
        });

        // 10. Comments table (self-referential response column)
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('comment_id')->primary();
            $table->foreignUuid('user')->constrained('profiles', 'profile_id')->nullOnDelete();
            $table->foreignUuid('chapter')->constrained('chapters', 'chapter_id')->cascadeOnDelete();
            $table->string('text',3000);
            $table->timestamps();
        });

        // 11. Add Foreginuuid to comment
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignUuid('response')->nullable()->constrained('comments', 'comment_id')->cascadeOnDelete();
        });

        // 12. Read history table
        Schema::create('read_histories', function (Blueprint $table) {
            $table->uuid('read_history_id')->primary();
            $table->foreignUuid('user')->constrained('profiles', 'profile_id')->nullOnDelete();
            $table->foreignUuid('work')->constrained('works', 'work_id')->nullOnDelete();
            $table->foreignUuid('chapter')->constrained('chapters', 'chapter_id')->nullOnDelete();
            $table->timestamps();
        });

        // 13. Read lists table
        Schema::create('read_lists', function (Blueprint $table) {
            $table->uuid('read_list_id')->primary();
            $table->string('name', 200)->default('Mi lista');
            $table->string('description', 3000)->nullable()->default('Nueva lista de lectura');
            $table->foreignUuid('user')->constrained('profiles', 'profile_id')->cascadeOnDelete();
            $table->timestamps();
        });

        // 14. Read lists – works pivot table
        Schema::create('read_lists_works', function (Blueprint $table) {
            $table->foreignUuid('read_list')->constrained('read_lists', 'read_list_id')->cascadeOnDelete();
            $table->foreignUuid('work')->constrained('works', 'work_id')->cascadeOnDelete();
            $table->timestamps();
        });

        // 15. Followers (following) table
        Schema::create('following', function (Blueprint $table) {
            $table->foreignUuid('follower_id')->constrained('profiles', 'profile_id')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('profiles', 'profile_id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations – drop tables in reverse order.
     */
    public function down(): void
    {
        Schema::dropIfExists('following');
        Schema::dropIfExists('read_lists_works');
        Schema::dropIfExists('read_lists');
        Schema::dropIfExists('read_histories');
        Schema::dropIfExists('genres_works');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('collections_works');
        Schema::dropIfExists('collections');
        Schema::dropIfExists('genre_works');
        Schema::dropIfExists('genres');
        Schema::dropIfExists('chapters');
        Schema::dropIfExists('works');
        Schema::dropIfExists('drafts');
        Schema::dropIfExists('wips');
        Schema::dropIfExists('age_restrictions');
    }
};
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
        Schema::create('project_type_chapter_blocks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_type_chapter_id')->constrained()->cascadeOnDelete();
            $table->enum('block_type', ['text', 'image'])->default('text');
            $table->enum('editable_by', ['admin', 'user', 'both'])->default('user');
            $table->text('prompt_text')->nullable();
            $table->longText('admin_content')->nullable();
            $table->enum('caption_position', ['above', 'below'])->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('project_chapter_blocks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_chapter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_type_chapter_block_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('block_type', ['text', 'image'])->default('text');
            $table->enum('editable_by', ['admin', 'user', 'both'])->default('user');
            $table->text('prompt_text')->nullable();
            $table->longText('admin_content')->nullable();
            $table->longText('user_text')->nullable();
            $table->string('image_path')->nullable();
            $table->enum('caption_position', ['above', 'below'])->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['partial_completed', 'completed'])->default('partial_completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_chapter_blocks');
        Schema::dropIfExists('project_type_chapter_blocks');
    }
};

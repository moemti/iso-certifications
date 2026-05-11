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
        Schema::table('project_type_chapters', function (Blueprint $table): void {
            $table->longText('admin_default_text')->nullable()->after('description');
            $table->boolean('is_required')->default(true)->after('sort_order');
            $table->boolean('is_user_editable')->default(true)->after('is_required');
        });

        Schema::table('project_chapters', function (Blueprint $table): void {
            $table->longText('admin_default_text')->nullable()->after('description');
            $table->longText('user_content')->nullable()->after('admin_default_text');
            $table->boolean('is_required')->default(true)->after('sort_order');
            $table->boolean('is_user_editable')->default(true)->after('is_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_chapters', function (Blueprint $table): void {
            $table->dropColumn([
                'admin_default_text',
                'user_content',
                'is_required',
                'is_user_editable',
            ]);
        });

        Schema::table('project_type_chapters', function (Blueprint $table): void {
            $table->dropColumn([
                'admin_default_text',
                'is_required',
                'is_user_editable',
            ]);
        });
    }
};

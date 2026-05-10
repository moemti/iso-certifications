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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->unsignedBigInteger('cui')->unique();
            $table->unsignedBigInteger('trade_register_number')->nullable();
            $table->string('address');
            $table->string('city', 100);
            $table->string('county', 100);
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 20);
            $table->string('email');
            $table->string('contact_person');
            $table->string('bank_account', 50)->nullable();
            $table->string('bank_name')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};

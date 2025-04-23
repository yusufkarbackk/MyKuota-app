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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('password');
            $table->string('phone_number');
            $table->string('status')->default('available');
            $table->float('quota')->nullable();
            $table->float('total_usage')->nullable();
            $table->string('chrome_profile')->default('');
            $table->string('profile_path')->default('');
            $table->string('update_status')->default('');
            $table->string('error_log')->default('');
            $table->boolean('is_complete')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};

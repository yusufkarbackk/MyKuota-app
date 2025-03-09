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
        Schema::create('log_terminates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts');
            $table->string('reason');
            $table->string('terminator_email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_terminates');
    }
};

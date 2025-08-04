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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 150)->unique();
            $table->string('password')->nullable();
            $table->string('provider', 50)->nullable();
            $table->string('provider_id', 150)->nullable();
            $table->text('avatar')->nullable();
            $table->enum('role', ['admin', 'user'])->default('user');

            $table->timestamp('email_verified_at')->nullable();
            $table->text('two_factor_secret')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
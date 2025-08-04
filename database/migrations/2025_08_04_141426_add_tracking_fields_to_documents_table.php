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
        Schema::table('documents', function (Blueprint $table) {
              $table->string('original_hash', 255)->nullable()->after('file_path');
            $table->enum('status', ['draft', 'pending', 'completed', 'archived'])->default('draft')->after('group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
             $table->dropColumn(['original_hash', 'status']);
        });
    }
};

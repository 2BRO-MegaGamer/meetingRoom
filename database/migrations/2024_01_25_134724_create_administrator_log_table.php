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
        Schema::create('administrator_log', function (Blueprint $table) {
            $table->id();
            $table->string('admin_name');
            $table->string('admin_id');
            $table->string('target_name')->nullable(true);
            $table->string('target_id')->nullable(true);
            $table->string('target_server_id')->nullable(true);
            $table->string('details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrator_log');
    }
};

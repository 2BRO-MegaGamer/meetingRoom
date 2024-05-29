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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('creator_id');
            $table->string('room_name')->default('This room has no name');
            $table->string('room_uuid')->unique();
            $table->string('Members')->nullable()->default(NULL);
            $table->string('MOD_member')->nullable()->default(NULL);
            $table->string('wait_to_accept')->nullable()->default(NULL);
            $table->string('accept_m')->nullable()->default(NULL);
            $table->string('deny_m')->nullable()->default(NULL);
            $table->string('removed_m')->nullable()->default(NULL);
            $table->string('banned_m')->nullable()->default(NULL);
            $table->string('warn_m')->nullable()->default(NULL);
            $table->longText('chat_messages')->nullable()->default(NULL);
            $table->longText('announcement')->nullable()->default(NULL);
            $table->string('type');
            $table->string('status')->default("Offline");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

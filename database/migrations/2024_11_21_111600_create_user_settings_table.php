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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_id');
            $table->string('repetition_time')->nullable();
            $table->integer('words_per_day')->nullable();
            $table->string('role')->nullable();
            $table->string('status')->nullable();
            $table->index('chat_id', 'user_settings_chat_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropIndex('user_settings_chat_id_index');  
        });
        Schema::dropIfExists('user_settings');
    }
};

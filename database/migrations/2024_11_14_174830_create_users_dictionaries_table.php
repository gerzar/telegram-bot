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
        Schema::create('users_dictionaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_id');
            $table->unsignedBigInteger('word_id');
            $table->datetime('last_check');
            $table->integer('success');
            $table->integer('fails');
            $table->boolean('learned');
            $table->index(['chat_id', 'word_id'], 'users_dictionaries_chat_id_word_id_index');
            $table->index(['chat_id', 'word_id', 'last_check'], 'users_dictionaries_chat_id_word_id_last_check_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('users_dictionaries', function (Blueprint $table) {
            $table->dropIndex('users_dictionaries_chat_id_word_id_index');  
            $table->dropUnique('users_dictionaries_chat_id_word_id_last_check_unique');  
        });

        Schema::dropIfExists('users_dictionaries');

        
    }
};

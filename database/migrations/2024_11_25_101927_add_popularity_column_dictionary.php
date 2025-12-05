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
        Schema::table('dictionaries', function (Blueprint $table) {
            $table->integer('popularity')->nullable();
            $table->index('popularity', 'word_popularity_dictionaries_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dictionaries', function (Blueprint $table) {
            $table->dropIndex('word_popularity_dictionaries_index');  
            $table->dropColumn('popularity');
        });
    }
};

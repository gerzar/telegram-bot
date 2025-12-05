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
        Schema::create('dictionaries', function (Blueprint $table) {
            $table->id();
            $table->string('word',255);
            $table->text('translation')->nullable();
            $table->text('definition')->nullable();
            $table->text('example')->nullable();
            $table->unique('word', 'dictionaries_unique_words');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('dictionaries', function (Blueprint $table) {
            $table->dropUnique('dictionaries_unique_words');  
        });
        Schema::dropIfExists('dictionaries');
        
    }
};

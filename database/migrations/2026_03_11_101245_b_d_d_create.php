<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('role', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->timestamps();
        });
       
        Schema::create('generic_instrument', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
        });
        Schema::create('type_training', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname', 50)->unique();
            $table->foreignId('role_id')->constrained('role');
        });
      
        Schema::create('training', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->date('date_training');
            $table->integer('duration');
            $table->foreignId('type_training_id')->constrained('type_training');
            $table->foreignId('users_id')->constrained('users');
        });
        Schema::create('instrument', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->foreignId('generic_instrument_id')->constrained('generic_instrument');
            $table->foreignId('users_id')->constrained('users');
        });
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->foreignId('users_id')->constrained('users');
        });
        Schema::create('sheet', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('link', 255);
            $table->foreignId('instrument_id')->constrained('instrument');
            $table->foreignId('users_id')->constrained('users');
        });

        Schema::create('is_part', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groups_id')->constrained('groups');
            $table->foreignId('users_id')->constrained('users');
        });
        
        Schema::create('share', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sheet_id')->constrained('sheet');
            $table->foreignId('groups_id')->constrained('groups');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('nickname');
            $table->dropColumn('role_id');
            });

        Schema::dropIfExists('share');
        Schema::dropIfExists('is_part');
        Schema::dropIfExists('sheet');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('instrument');
        Schema::dropIfExists('training');
        Schema::dropIfExists('type_training');
        Schema::dropIfExists('generic_instrument');
        Schema::dropIfExists('role');
    }
};

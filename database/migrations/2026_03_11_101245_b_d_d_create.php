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
        Schema::create('generic_instrument', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
        });
        Schema::create('type_training', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
        });
        Schema::create('role', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
        });
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('surname', 50);
            $table->string('nickname', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password', 100);
            $table->foreignId('role_id')->constrained('role');
        });
        Schema::create('training', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->date('date_training');
            $table->integer('duration');
            $table->foreignId('type_training_id')->constrained('type_training');
            $table->foreignId('user_id')->constrained('user');
        });
        Schema::create('instrument', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->foreignId('generic_instrument_id')->constrained('generic_instrument');
            $table->foreignId('user_id')->constrained('user');
        });
        Schema::create('group', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->foreignId('user_id')->constrained('user');
        });
        Schema::create('sheet', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('link', 255);
            $table->foreignId('instrument_id')->constrained('instrument');
            $table->foreignId('user_id')->constrained('user');
        });

        Schema::create('is_part', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('group');
            $table->foreignId('user_id')->constrained('user');
        });
        
        Schema::create('share', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sheet_id')->constrained('sheet');
            $table->foreignId('group_id')->constrained('group');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

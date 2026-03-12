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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->timestamps();
        });
       
        Schema::create('generic_instruments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
        });
        Schema::create('type_trainings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname', 50)->nullable();
            $table->foreignId('role_id')->constrained('roles');
        });
      
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->date('date_training');
            $table->integer('duration');
            $table->foreignId('type_training_id')->constrained('type_trainings');
            $table->foreignId('users_id')->constrained('users');
        });
        Schema::create('instruments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->foreignId('generic_instrument_id')->constrained('generic_instruments');
            $table->foreignId('users_id')->constrained('users');
        });
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->foreignId('users_id')->constrained('users');
        });
        Schema::create('sheets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('link', 255);
            $table->foreignId('instrument_id')->constrained('instruments');
            $table->foreignId('users_id')->constrained('users');
        });

        Schema::create('is_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groups_id')->constrained('groups');
            $table->foreignId('users_id')->constrained('users');
        });
        
        Schema::create('shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sheet_id')->constrained('sheets');
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

        Schema::dropIfExists('shares');
        Schema::dropIfExists('is_parts');
        Schema::dropIfExists('sheets');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('instruments');
        Schema::dropIfExists('trainings');
        Schema::dropIfExists('type_trainings');
        Schema::dropIfExists('generic_instruments');
        Schema::dropIfExists('roles');
    }
};

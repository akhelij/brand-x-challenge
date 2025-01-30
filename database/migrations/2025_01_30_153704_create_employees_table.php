<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('import_reference')->unique();
            $table->string('username');
            $table->string('name_prefix')->nullable();
            $table->string('first_name');
            $table->string('middle_initial')->nullable();
            $table->string('last_name');
            $table->string('gender')->nullable();
            $table->string('email');
            $table->date('date_of_birth');
            $table->time('time_of_birth')->nullable();
            $table->decimal('age_in_years', 5, 2)->nullable();
            $table->date('date_of_joining');
            $table->decimal('age_in_company_years', 5, 2)->nullable();
            $table->string('phone_number')->nullable();
            $table->string('place_name')->nullable();
            $table->string('county')->nullable();
            $table->string('city')->nullable();
            $table->string('zip')->nullable();
            $table->string('region')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('username');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

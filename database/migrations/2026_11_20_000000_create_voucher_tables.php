<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 80);
            $table->string('last_name', 80);
            $table->string('classroom', 80);
            $table->string('school_shift', 16);
            $table->string('contact', 160)->nullable();
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('meal_assignments', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday');
            $table->string('meal', 16);
            $table->primary(['student_id', 'weekday', 'meal']);
        });

        Schema::create('calendar_exclusions', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 20);
            $table->string('description', 120);
            $table->date('starts_on')->index();
            $table->date('ends_on');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_exclusions');
        Schema::dropIfExists('meal_assignments');
        Schema::dropIfExists('students');
    }
};

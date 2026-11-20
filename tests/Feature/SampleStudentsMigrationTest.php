<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;

class SampleStudentsMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sample_students_are_loaded_only_into_an_empty_student_table(): void
    {
        $this->assertDatabaseCount('students', 70);
        $this->assertDatabaseCount('meal_assignments', 350);
        $this->assertSame(70, DB::table('students')->where('active', true)->count());

        $migration = require database_path('migrations/2026_11_20_000001_add_sample_students.php');
        $migration->up();

        $this->assertDatabaseCount('students', 70);
        $this->assertDatabaseCount('meal_assignments', 350);
    }
}

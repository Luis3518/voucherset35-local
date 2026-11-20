<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('students')->exists()) {
            return;
        }

        $firstNames = ['Ana', 'Bruno', 'Camila', 'Diego', 'Elena', 'Facundo', 'Gabriela', 'Hugo', 'Inés', 'Joaquín'];
        $lastNames = ['García', 'Pérez', 'González', 'Rodríguez', 'López', 'Martínez', 'Fernández'];
        $now = now();

        foreach ($lastNames as $group => $lastName) {
            foreach ($firstNames as $index => $firstName) {
                $number = $group * count($firstNames) + $index;
                $shift = $number % 2 === 0 ? 'morning' : 'afternoon';
                $studentId = DB::table('students')->insertGetId([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'classroom' => ($number % 6 + 1).'° '.($group % 2 === 0 ? 'A' : 'B'),
                    'school_shift' => $shift,
                    'contact' => null,
                    'starts_on' => $now->copy()->startOfYear()->toDateString(),
                    'ends_on' => null,
                    'active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach (range(1, 5) as $weekday) {
                    DB::table('meal_assignments')->insert([
                        'student_id' => $studentId,
                        'weekday' => $weekday,
                        'meal' => $shift === 'morning' ? 'breakfast' : 'snack',
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Los alumnos pueden haber sido modificados; no borrar datos al revertir.
    }
};

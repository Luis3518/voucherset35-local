<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealAssignment extends Model
{
    public const WEEKDAYS = [1 => 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

    public const MEALS = [
        'breakfast' => 'Desayuno',
        'lunch' => 'Almuerzo',
        'snack' => 'Merienda',
        'dinner' => 'Cena',
    ];

    public $incrementing = false;
    public $timestamps = false;
}

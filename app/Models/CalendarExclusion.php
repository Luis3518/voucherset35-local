<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarExclusion extends Model
{
    public const KINDS = [
        'holiday' => 'Feriado',
        'no_activity' => 'Sin actividad',
        'vacation' => 'Vacaciones',
        'recess' => 'Receso escolar',
    ];

    protected $fillable = ['kind', 'description', 'starts_on', 'ends_on'];

    protected function casts(): array
    {
        return ['starts_on' => 'immutable_date', 'ends_on' => 'immutable_date'];
    }
}

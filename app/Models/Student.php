<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    public const SHIFTS = ['morning' => 'Mañana', 'afternoon' => 'Tarde', 'night' => 'Noche'];

    protected $fillable = [
        'first_name', 'last_name', 'classroom', 'school_shift', 'contact', 'starts_on', 'ends_on', 'active',
    ];

    protected function casts(): array
    {
        return ['starts_on' => 'immutable_date', 'ends_on' => 'immutable_date', 'active' => 'boolean'];
    }

    public function getFullNameAttribute(): string
    {
        return $this->last_name.', '.$this->first_name;
    }

    #[Scope]
    protected function alphabetical(Builder $query): void
    {
        $query->orderByRaw('last_name COLLATE NOCASE, first_name COLLATE NOCASE')->orderBy('id');
    }

    public function mealAssignments(): HasMany
    {
        return $this->hasMany(MealAssignment::class);
    }
}

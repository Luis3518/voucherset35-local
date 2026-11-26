<?php

namespace App\Services;

use App\Models\CalendarExclusion;
use App\Models\MealAssignment;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection as Students;
use Illuminate\Support\Collection;

class VoucherGenerator
{
    public function generate(Students $students, CarbonImmutable $from, CarbonImmutable $through): Collection
    {
        $exclusions = CalendarExclusion::where('starts_on', '<=', $through->endOfDay()->toDateTimeString())
            ->where('ends_on', '>=', $from->toDateString())
            ->orderBy('starts_on')
            ->get();

        $schoolDays = [];
        $exclusionIndex = 0;

        for ($date = $from; $date <= $through; $date = $date->addDay()) {
            while (isset($exclusions[$exclusionIndex]) && $exclusions[$exclusionIndex]->ends_on < $date) {
                $exclusionIndex++;
            }

            $exclusion = $exclusions[$exclusionIndex] ?? null;
            if ($date->isWeekday() && ($exclusion === null || $exclusion->starts_on > $date)) {
                $schoolDays[] = $date;
            }
        }

        $vouchers = collect();
        foreach ($students as $student) {
            $assignments = $student->mealAssignments->groupBy('weekday')->map->pluck('meal')->map->flip();

            foreach ($schoolDays as $date) {
                if ($date < $student->starts_on || ($student->ends_on !== null && $date > $student->ends_on)) {
                    continue;
                }

                $meals = $assignments[$date->dayOfWeekIso] ?? null;
                foreach (array_intersect_key(MealAssignment::MEALS, $meals?->all() ?? []) as $label) {
                    $vouchers->push([
                        'student_id' => $student->id,
                        'name' => $student->full_name,
                        'date' => $date->format('d/m/Y'),
                        'meal' => $label,
                    ]);
                }
            }
        }

        return $vouchers;
    }
}

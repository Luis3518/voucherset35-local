<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

class VoucherFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function beforeRefreshingDatabase(): void
    {
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
    }

    public function test_voucher_flow_respects_assignments_exclusions_selection_and_scholarship_dates(): void
    {
        $this->withoutVite();
        $student = $this->createStudent(['starts_on' => '2026-11-17', 'ends_on' => '2026-11-23']);
        $unselected = $this->createStudent(['first_name' => 'Bruno']);
        $this->createStudent(['first_name' => 'Clara', 'active' => false]);

        $this->get(route('students.index', ['buscar' => 'Pérez']))->assertOk()->assertSee('Pérez, Ana');
        $this->get(route('students.edit', $student))->assertOk()->assertSee('Eliminar alumno');
        $this->get(route('meals.index'))->assertOk()->assertSee('Guardar cambios')
            ->assertSee('"weekdays":{"1":"Lunes"', false);
        $this->get(route('calendar.index'))->assertOk();
        $this->get(route('vouchers.index'))->assertOk()->assertDontSee('Pérez, Clara');

        $this->post(route('calendar.store'), [
            'kind' => 'holiday',
            'description' => 'Feriado de un día',
            'starts_on' => '2026-11-20',
        ])->assertRedirect(route('calendar.index'));

        $this->post(route('calendar.store'), [
            'kind' => 'recess',
            'description' => 'Receso escolar',
            'starts_on' => '2026-11-18',
            'ends_on' => '2026-11-19',
        ])->assertRedirect(route('calendar.index'));

        $changes = [];
        foreach (range(1, 5) as $weekday) {
            foreach (['breakfast', 'lunch', 'snack', 'dinner'] as $meal) {
                $changes[] = ['student_id' => $student->id, 'weekday' => $weekday, 'meal' => $meal, 'assigned' => true];
            }
        }
        $changes[] = ['student_id' => $unselected->id, 'weekday' => 2, 'meal' => 'dinner', 'assigned' => true];

        $this->patchJson(route('meals.update'), ['changes' => $changes])->assertNoContent();
        $this->patchJson(route('meals.update'), ['changes' => [
            ['student_id' => $student->id, 'weekday' => 1, 'meal' => 'dinner', 'assigned' => false],
        ]])->assertNoContent();

        $pdf = $this->postJson(route('vouchers.generate'), [
            'starts_on' => '2026-11-16',
            'ends_on' => '2026-11-24',
            'student_ids' => [$student->id],
        ])->assertOk()->assertHeader('X-Voucher-Count', '7')
            ->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $pdf->getContent());
        $this->assertMatchesRegularExpression('~/Type\s*/Pages\s*/Kids\s*\[[^\]]+\]\s*/Count\s+1\b~', $pdf->getContent());

        $this->postJson(route('vouchers.generate'), [
            'starts_on' => '2026-11-20',
            'ends_on' => '2026-11-20',
            'student_ids' => [$student->id],
        ])->assertUnprocessable()->assertJsonValidationErrors('student_ids');

        $this->delete(route('students.destroy', $student))->assertRedirect(route('students.index'));
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('meal_assignments', ['student_id' => $student->id]);
        $this->assertDatabaseHas('meal_assignments', ['student_id' => $unselected->id]);
    }

    public function test_pdf_paginates_after_twelve_vouchers(): void
    {
        $student = $this->createStudent();
        $changes = [];
        foreach (range(1, 4) as $weekday) {
            foreach (['breakfast', 'lunch', 'snack', 'dinner'] as $meal) {
                $changes[] = ['student_id' => $student->id, 'weekday' => $weekday, 'meal' => $meal, 'assigned' => true];
            }
        }
        $changes = array_slice($changes, 0, 13);
        $this->patchJson(route('meals.update'), ['changes' => $changes])->assertNoContent();

        $pdf = $this->postJson(route('vouchers.generate'), [
            'starts_on' => '2026-11-16',
            'ends_on' => '2026-11-19',
            'student_ids' => [$student->id],
        ])->assertOk()->assertHeader('X-Voucher-Count', '13');

        $this->assertMatchesRegularExpression('~/Type\s*/Pages\s*/Kids\s*\[[^\]]+\]\s*/Count\s+2\b~', $pdf->getContent());
        $this->assertMatchesRegularExpression('~/MediaBox\s*\[0(?:\.0+)?\s+0(?:\.0+)?\s+595\.\d+\s+841\.\d+\]~', $pdf->getContent());
    }

    public function test_each_student_starts_on_a_new_page_even_with_the_same_name(): void
    {
        $first = $this->createStudent();
        $second = $this->createStudent();
        $changes = [];
        foreach (range(1, 4) as $weekday) {
            foreach (['breakfast', 'lunch', 'snack', 'dinner'] as $meal) {
                $changes[] = ['student_id' => $first->id, 'weekday' => $weekday, 'meal' => $meal, 'assigned' => true];
            }
        }
        $changes = array_slice($changes, 0, 13);
        $changes[] = ['student_id' => $second->id, 'weekday' => 1, 'meal' => 'breakfast', 'assigned' => true];
        $this->patchJson(route('meals.update'), ['changes' => $changes])->assertNoContent();

        $pdf = $this->postJson(route('vouchers.generate'), [
            'starts_on' => '2026-11-16',
            'ends_on' => '2026-11-19',
            'student_ids' => [$first->id, $second->id],
        ])->assertOk()->assertHeader('X-Voucher-Count', '14');

        $this->assertMatchesRegularExpression('~/Type\s*/Pages\s*/Kids\s*\[[^\]]+\]\s*/Count\s+3\b~', $pdf->getContent());
    }

    private function createStudent(array $overrides = []): Student
    {
        $this->post(route('students.store'), array_replace([
            'first_name' => 'Ana',
            'last_name' => 'Pérez',
            'classroom' => '2.º A',
            'school_shift' => 'morning',
            'starts_on' => '2026-11-01',
            'active' => true,
        ], $overrides))->assertRedirect(route('students.index'));

        return Student::latest('id')->firstOrFail();
    }
}

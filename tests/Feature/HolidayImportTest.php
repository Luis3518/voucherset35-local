<?php

namespace Tests\Feature;

use App\Models\CalendarExclusion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class HolidayImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_preserves_existing_exclusions_and_can_be_repeated(): void
    {
        CalendarExclusion::create(['kind' => 'recess', 'description' => 'Carga manual', 'starts_on' => '2026-01-01', 'ends_on' => '2026-01-02']);
        Http::fake(['api.argentinadatos.com/*' => Http::response([
            ['fecha' => '2026-01-01', 'nombre' => 'Año Nuevo', 'tipo' => 'inamovible'],
            ['fecha' => '2026-03-24', 'nombre' => 'Día de la Memoria', 'tipo' => 'inamovible'],
        ])]);

        $this->post(route('calendar.import-holidays'), ['year' => 2026])->assertRedirect()->assertSessionHasNoErrors();
        $this->post(route('calendar.import-holidays'), ['year' => 2026])->assertRedirect()->assertSessionHasNoErrors();

        Http::assertSent(fn ($request) => $request->url() === 'https://api.argentinadatos.com/v1/feriados/2026');
        $this->assertDatabaseCount('calendar_exclusions', 2);
        $this->assertDatabaseHas('calendar_exclusions', ['description' => 'Carga manual', 'kind' => 'recess']);
        $holiday = CalendarExclusion::where('description', 'Día de la Memoria')->firstOrFail();
        $this->put(route('calendar.update', $holiday), ['kind' => 'holiday', 'description' => 'Editado', 'starts_on' => '2026-03-24'])->assertRedirect();
        $this->post(route('calendar.import-holidays'), ['year' => 2026])->assertRedirect();
        $this->assertDatabaseCount('calendar_exclusions', 2);
        $this->assertDatabaseHas('calendar_exclusions', ['description' => 'Editado']);
    }

    public function test_invalid_or_unavailable_response_does_not_change_calendar(): void
    {
        Http::fake(['*' => Http::response([['fecha' => '2026-01-01', 'nombre' => 'Correcto'], ['fecha' => '2027-01-01', 'nombre' => 'Incorrecto']])]);
        $this->post(route('calendar.import-holidays'), ['year' => 2026])->assertSessionHasErrors('year');
        $this->assertDatabaseCount('calendar_exclusions', 0);
        Http::fake(['*' => fn () => throw new ConnectionException]);
        $this->post(route('calendar.import-holidays'), ['year' => 2026])->assertSessionHasErrors('year');
        $this->assertDatabaseCount('calendar_exclusions', 0);
    }
}

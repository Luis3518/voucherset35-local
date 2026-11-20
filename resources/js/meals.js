import { TabulatorFull as Tabulator } from 'tabulator-tables';
import 'tabulator-tables/dist/css/tabulator_bootstrap5.min.css';
import { responseError, sendJson } from './request.js';

export function setupMeals(grid) {
    const { rows, weekdays, meals } = JSON.parse(document.querySelector('#meal-data').textContent);
    const save = document.querySelector('#save-meals');
    const status = document.querySelector('#meal-status');
    const error = document.querySelector('#meal-error');
    const search = document.querySelector('#meal-search');
    const saved = new Map();
    const changes = new Map();

    function setStatus(text = '') {
        save.disabled = changes.size === 0;
        status.textContent = text || (changes.size ? `Cambios pendientes: ${changes.size}` : '');
    }

    const dayColumns = Object.entries(weekdays).map(([day, dayLabel]) => ({
        title: dayLabel,
        columns: Object.entries(meals).map(([meal, mealLabel]) => ({
            title: mealLabel,
            field: `d${day}_${meal}`,
            headerSort: false,
            hozAlign: 'center',
            headerHozAlign: 'center',
            width: 83,
            cssClass: 'meal-cell',
            formatter(cell) {
                const row = cell.getRow().getData();
                const field = cell.getField();
                const key = `${row.id}:${field}`;
                const input = document.createElement('input');
                input.type = 'checkbox';
                input.className = 'form-check-input';
                input.checked = Boolean(cell.getValue());
                input.setAttribute('aria-label', `${row.name}, ${dayLabel}, ${mealLabel}`);
                if (!saved.has(key)) {
                    saved.set(key, input.checked);
                }

                input.addEventListener('change', () => {
                    const assigned = input.checked;
                    if (assigned === saved.get(key)) {
                        changes.delete(key);
                    } else {
                        changes.set(key, { student_id: row.id, weekday: Number(day), meal, assigned });
                    }
                    const focused = document.activeElement === input;
                    cell.setValue(assigned);
                    if (focused) {
                        cell.getElement().querySelector('input').focus({ preventScroll: true });
                    }
                    setStatus();
                });

                return input;
            },
        })),
    }));

    const table = new Tabulator(grid, {
        data: rows,
        height: 'min(62vh, 570px)',
        layout: 'fitDataStretch',
        placeholder: 'No hay alumnos que coincidan con la búsqueda.',
        columnDefaults: { formatter: 'plaintext' },
        columns: [
            { title: 'Alumno', field: 'name', frozen: true, width: 210 },
            { title: 'Curso', field: 'classroom', width: 110 },
            { title: 'Turno', field: 'shift', width: 95 },
            {
                title: 'Estado', field: 'active', width: 90,
                formatter: (cell) => cell.getValue() ? 'Activo' : 'Inactivo',
            },
            ...dayColumns,
        ],
    });

    const normalize = (value) => value.toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '');
    search.addEventListener('input', () => {
        const query = normalize(search.value.trim());
        table.setFilter((row) => normalize(`${row.name} ${row.classroom} ${row.shift}`).includes(query));
    });

    save.addEventListener('click', async () => {
        save.disabled = true;
        grid.inert = true;
        error.hidden = true;
        status.textContent = 'Guardando…';

        try {
            const response = await sendJson(grid.dataset.updateUrl, 'PATCH', { changes: [...changes.values()] });
            if (!response.ok) {
                throw await responseError(response, 'No se pudieron guardar las asignaciones.');
            }
            changes.forEach((change, key) => saved.set(key, change.assigned));
            changes.clear();
            setStatus('Asignaciones guardadas.');
        } catch (failure) {
            error.textContent = failure instanceof TypeError
                ? 'No se pudo conectar con el sistema. Volvé a intentar.'
                : failure.message;
            error.hidden = false;
            setStatus();
        } finally {
            grid.inert = false;
        }
    });

    window.addEventListener('beforeunload', (event) => {
        if (changes.size) {
            event.preventDefault();
            event.returnValue = true;
        }
    });
}

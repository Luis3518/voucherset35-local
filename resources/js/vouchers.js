import { responseError, sendJson } from './request.js';

export function setupVouchers(form) {
    const period = form.querySelector('#period');
    const periods = JSON.parse(period.dataset.periods);
    const start = form.querySelector('#voucher-start');
    const end = form.querySelector('#voucher-end');
    const students = [...form.querySelectorAll('[name="student_ids[]"]')];
    const selectionCount = form.querySelector('#selection-count');
    const button = form.querySelector('#generate-vouchers');
    const error = form.querySelector('#voucher-error');
    const progress = form.querySelector('#voucher-progress');
    const fieldsets = [...form.querySelectorAll('fieldset')];
    const preview = document.querySelector('#voucher-preview');
    const frame = document.querySelector('#voucher-frame');
    const download = document.querySelector('#voucher-download');
    const print = document.querySelector('#voucher-print');
    const total = document.querySelector('#voucher-total');
    let pdfUrl;

    function updateSelection() {
        selectionCount.textContent = `${students.filter((student) => student.checked).length} de ${students.length} seleccionados`;
    }

    function clearPreview() {
        preview.hidden = true;
        if (pdfUrl) {
            frame.removeAttribute('src');
            download.removeAttribute('href');
            print.removeAttribute('href');
            URL.revokeObjectURL(pdfUrl);
            pdfUrl = undefined;
        }
    }

    function selectAll(checked) {
        students.forEach((student) => { student.checked = checked; });
        updateSelection();
        clearPreview();
    }

    period.addEventListener('change', () => {
        if (period.value !== 'custom') {
            [start.value, end.value] = periods[period.value];
        }
    });
    [start, end].forEach((field) => {
        field.addEventListener('change', () => { period.value = 'custom'; });
    });

    form.querySelector('#select-all').addEventListener('click', () => selectAll(true));
    form.querySelector('#select-none').addEventListener('click', () => selectAll(false));
    form.addEventListener('change', () => {
        updateSelection();
        clearPreview();
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearPreview();
        error.hidden = true;
        progress.hidden = false;
        button.disabled = true;
        button.textContent = 'Generando…';
        fieldsets.forEach((fieldset) => { fieldset.disabled = true; });

        const data = {
            starts_on: start.value,
            ends_on: end.value,
            student_ids: students.filter((student) => student.checked).map((student) => Number(student.value)),
        };

        try {
            const response = await sendJson(form.action, 'POST', data);
            if (!response.ok || !response.headers.get('content-type')?.startsWith('application/pdf')) {
                throw await responseError(response, 'No se pudo generar el PDF.');
            }

            pdfUrl = URL.createObjectURL(await response.blob());
            const count = Number(response.headers.get('X-Voucher-Count'));
            total.textContent = count === 1 ? '1 voucher generado' : `${count} vouchers generados`;
            download.download = `vouchers-${data.starts_on}_${data.ends_on}.pdf`;
            [download, print].forEach((link) => { link.href = pdfUrl; });
            frame.src = pdfUrl;
            preview.hidden = false;
            preview.scrollIntoView({ block: 'start' });
        } catch (failure) {
            error.className = 'alert alert-danger mt-3';
            error.textContent = failure instanceof TypeError
                ? 'No se pudo conectar con el sistema. Volvé a intentar.'
                : failure.message;
            error.hidden = false;
        } finally {
            progress.hidden = true;
            fieldsets.forEach((fieldset) => { fieldset.disabled = false; });
            button.disabled = false;
            button.textContent = 'Generar vouchers';
        }
    });
}

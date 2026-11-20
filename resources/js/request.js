export function sendJson(url, method, data) {
    return fetch(url, {
        method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(data),
    });
}

export async function responseError(response, fallback) {
    if (response.status === 419) {
        return new Error('La sesión venció. Recargá la página y volvé a intentar.');
    }

    if (response.status === 422) {
        const data = await response.json();
        return new Error(Object.values(data.errors ?? {}).flat().join(' ') || fallback);
    }

    return new Error(fallback);
}

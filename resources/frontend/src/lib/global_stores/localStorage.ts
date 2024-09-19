import { writable } from "svelte/store";

export function persistent(key: string, value: string) {
    // Verificamos si estamos en el navegador
    let localStoragee: Storage | null = null;
    
    if (typeof window !== 'undefined') {
        localStoragee = localStorage;
    }

    const storedValue = localStoragee ? localStoragee.getItem(key) : null;
    const data = storedValue !== null ? storedValue : value;

    const store = writable(data);

    store.subscribe(value => {
        if (localStoragee) {
            localStoragee.setItem(key, value);
        }
    });

    return store;
}
<script lang="ts">
    import Select from "$lib/components/Select.svelte";
import type { Pagination } from "$lib/interfaces/pagination";
    import type { Product } from "$lib/interfaces/product";
	import { Icon } from "svelte-icons-pack";
	import { AiOutlineSearch } from "svelte-icons-pack/ai";
	import { writable, type Writable } from "svelte/store";
    
    export let nameToSearch: Writable<string>;
    export let codeToSearch: Writable<string>;

    export let requestFunctions: {
        loadProducts: (url: string | undefined) => Promise<void>;
        getProductById: () => Promise<void>;
    }

    function updateNameToSearch (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        $nameToSearch = value;
    }

    function updateCodeToSearch (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        $codeToSearch = value;
    }

    function detectEnter (e: KeyboardEvent) {
        if (e.key === "Enter") {
            searchProducts();
        }
    }

    async function searchProducts () {
        requestFunctions.loadProducts(`/api/products?code=${$codeToSearch}&name=${$nameToSearch}`)
        
    }

</script>
<div class="flex flex-col gap-4 justify-center">
    <section class="flex flex-row gap-2 w-full">
        <div class="flex-1">
            <button class="w-full border border-[--color-theme-1] rounded-md text-start p-1.5 bg-slate-50 text-[--color-theme-1] shadow-md">
                Búsqueda general
            </button>
        </div>
        <div class="flex-1">
            <button class="w-full border border-[--color-theme-1] rounded-md text-start p-1.5 bg-[--color-theme-1] text-slate-50 shadow-sm">
                Por autorización
            </button>
        </div>
    </section>
    <section class="flex flex-col gap-4 justify-center border border-[--color-theme-1] rounded-md bg-slate-50 p-4 shadow-md">
        <div class="flex flex-wrap gap-2 justify-center place-items-center">
            <div class="flex flex-row gap-2 flex-1">
                <label for="codigo">
                    Fecha inicio:
                </label>
                <input name="codigo" class="flex-grow border border-[--color-border] bg-transparent rounded-md px-1 ml-auto" type="date" value={$codeToSearch} on:input={(e)=>updateCodeToSearch(e)} on:keypress={(e)=>detectEnter(e)}>
            </div>
            <div class="flex flex-row gap-2 flex-1">
                <label for="nombre">
                    Fecha fin:
                </label>
                <input name="nombre" class="flex-grow border border-[--color-border] bg-transparent rounded-md px-1 ml-auto" type="date" value={$nameToSearch} on:input={(e)=>updateNameToSearch(e)} on:keypress={(e)=>detectEnter(e)}>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 justify-center place-items-center">
            <div class="flex flex-row gap-2 flex-1">
                <label for="codigo">
                    Identificación:
                </label>
                <input name="codigo" class="flex-grow border border-[--color-border] bg-transparent rounded-md px-1 ml-auto" type="text" value={$codeToSearch} on:input={(e)=>updateCodeToSearch(e)} on:keypress={(e)=>detectEnter(e)}>
            </div>
            <div class="flex flex-row gap-2 flex-1">
                <label for="nombre">
                    Comprobante:
                </label>
                <div class="flex-grow flex place-items-center">
                    <Select alert={false} options={[{id:1, name: 'Factura'}]} optionSelected={writable(0)} wFull={true} />
                </div>
            </div>
        </div>
    </section>
    <section class="flex justify-center">
        <button class="flex flex-row gap-2 place-items-center bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600 text-slate-50" on:click={searchProducts}>
            <Icon src={AiOutlineSearch} />
            <span>Buscar</span>
        </button>
    </section>

</div>
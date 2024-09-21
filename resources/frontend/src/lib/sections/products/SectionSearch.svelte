<script lang="ts">
    import type { Product } from "$lib/interfaces/product";
	import { Icon } from "svelte-icons-pack";
	import { AiOutlineSearch } from "svelte-icons-pack/ai";
	import type { Writable } from "svelte/store";

    export let products: Writable<Product[]>;
    export let nameToSearch: Writable<string>;
    export let codeToSearch: Writable<string>;

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

    async function searchProducts () {
        const res = await fetch(`/api/products?code=${$codeToSearch}&name=${$nameToSearch}`,
            {
                headers: {'Accept': 'application/json'}
            }
        );
        $products = []
        const data = await res.json();
        if (Array.isArray(data)) {
            $products = data
        }
    }

</script>
<div class="flex flex-col gap-4 justify-center">
    <section class="flex flex-wrap gap-4 justify-center">
        <div class="flex flex-row gap-2">
            <label for="codigo">
                Código:
            </label>
            <input name="codigo" class="border border-[--color-border] bg-transparent rounded-md px-1 ml-auto" type="text" value={$codeToSearch} on:input={(e)=>updateCodeToSearch(e)}>
        </div>
        <div class="flex flex-row gap-2">
            <label for="nombre">
                Nombre:
            </label>
            <input name="nombre" class="border border-[--color-border] bg-transparent rounded-md px-1 ml-auto" type="text" value={$nameToSearch} on:input={(e)=>updateNameToSearch(e)}>
        </div>
    </section>
    <section class="flex justify-center">
        <button class="flex flex-row gap-2 place-items-center bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600 text-slate-50" on:click={searchProducts}>
            <Icon src={AiOutlineSearch} />
            <span>Buscar</span>
        </button>
    </section>

</div>
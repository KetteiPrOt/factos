<script lang="ts">
	import type { Product } from "$lib/interfaces/product";
	import { onMount } from "svelte";


	import { Icon } from "svelte-icons-pack";
	import { AiFillCaretDown, AiFillFastBackward, AiFillFastForward } from "svelte-icons-pack/ai";
	import { BiDownload } from "svelte-icons-pack/bi";
	import type { Writable } from "svelte/store";
    import { fade, fly, slide } from "svelte/transition";

    export let products: Writable<Product[]>;


    async function loadProducts () {
        const res = await fetch('/api/products',
            {
                headers: {'Accept': 'application/json'}
            }
        );
        
        $products = []
        const data = await res.json();
        if (Array.isArray(data)) {
            $products = data
        }
    };
    
    
    
    onMount(loadProducts)

</script>
<section class="max-w-full">
    <h3 class="font-semibold text-lg px-3 py-1">
        Lista de productos
    </h3>
    <div class="block w-full  rounded-md max-w-full overflow-x-auto">
        <table class="max-w-full w-full border-collapse text-center">
            <thead class="bg-[--color-theme-1]">
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Valor</th>
                    <th>IVA</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                {#each $products as product}
                <tr in:fade>
                    <td>{product.code}</td>
                    <td>{product.name}</td>
                    <td>{product.price}</td>
                    <td>{product.vat_rate}</td>
                    <td>
                        <button class="flex flex-row gap-2 place-items-center bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600 text-slate-50 m-auto">
                            <Icon src={AiFillCaretDown}/>
                        </button>
                    </td>
                </tr>
                {/each}
            </tbody>
        </table>
    </div>
    <div class="w-full bg-[--color-theme-1] flex flex-row justify-center text-slate-50 p-1 place-items-center gap-4 rounded-b-md">
        <button class="flex flex-row gap-2 place-items-center bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600 text-slate-50">
            <Icon src={AiFillFastBackward} size={20}/>
        </button>
        <span>
            ( 1 de X )
        </span>
        <button class="flex flex-row gap-2 align-middle bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600 text-slate-50">
            <Icon src={AiFillFastForward} size={20}/>
        </button>
    </div>
    <button class="flex flex-row gap-2 text-[--color-theme-1] mt-2 px-3 py-1 rounded-md border border-transparent hover:border-[--color-theme-1] hover:bg-slate-200">
        <Icon src={BiDownload} size={20}/>
        <span>Descargar reporte</span>
    </button>
</section>

<style>
    th {
        border: .5px solid var(--color-border);
        color: #f8fafc;
        padding: .5rem;
    }
    td {
        border: .5px solid var(--color-border);
        padding: .5rem;
    }
    tbody tr:last-child td:first-child {
        border-radius: 0%;
    } 
</style>
<script lang="ts">
	import type { Product, ProductGet } from "$lib/interfaces/product";
	import { onMount } from "svelte";


	import { Icon } from "svelte-icons-pack";
	import { AiFillCaretDown, AiFillFastBackward, AiFillFastForward } from "svelte-icons-pack/ai";
	import { BiDownload } from "svelte-icons-pack/bi";
	import type { Writable } from "svelte/store";
    import { fade, fly, slide } from "svelte/transition";

    export let requestFunctions: {
        loadProducts: () => Promise<void>;
        getProductById: () => Promise<void>;
    }

    export let products: Writable<Product[]>;
    export let productSelected: Writable<ProductGet>;

    export let toogleModalViewProductVisible: ()=>void;

    export let idToSearch: Writable<string>;

    

    function selectProduct (e: MouseEvent) {
        const target = e.target as HTMLButtonElement;
        const td = target.parentElement as HTMLTableCellElement
        const tr = td.parentElement as HTMLTableRowElement
        
        if (tr.dataset.id) {
            $idToSearch = tr.dataset.id;
            $productSelected = { code: "", name: "", price: 0, vat_rate_id: 0 };
            requestFunctions.getProductById();
            toogleModalViewProductVisible();
        }
    }
   
    
    
    
    onMount(requestFunctions.loadProducts)

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
                    <!-- <th>Acciones</th> -->
                </tr>
            </thead>
            <tbody>
                {#each $products as product}
                <tr in:fade class="hover:bg-slate-300" data-id="{product.id}">
                    <td>
                        <button class="w-full h-full p-2" on:click={(e)=>selectProduct(e)}>
                            {product.code}
                        </button>
                    </td>
                    <td>
                        <button class="w-full h-full p-2" on:click={(e)=>selectProduct(e)}>
                            {product.name}
                        </button>
                    </td>
                    <td>
                        <button class="w-full h-full p-2" on:click={(e)=>selectProduct(e)}>
                            {product.price}
                        </button>
                    </td>
                    <td>
                        <button class="w-full h-full p-2" on:click={(e)=>selectProduct(e)}>
                            {product.vat_rate}
                        </button>
                    </td>
                    <!-- <td>
                        <button class="flex flex-row gap-2 place-items-center bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600 text-slate-50 m-auto">
                            <Icon src={AiFillCaretDown}/>
                        </button>
                    </td> -->
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
        /* padding: .5rem; */
    }
    tbody tr:last-child td:first-child {
        border-radius: 0%;
    } 
</style>
<script lang="ts">
    import type { ReceiptInvoice } from "$lib/interfaces/invoice";
    import type { Pagination, PaginationReceiptInvonces } from "$lib/interfaces/pagination";
	import type { Product, ProductGet } from "$lib/interfaces/product";
	import { onMount } from "svelte";

	import { Icon } from "svelte-icons-pack";
	import { AiFillFastBackward, AiFillFastForward } from "svelte-icons-pack/ai";
	import { BiDownload } from "svelte-icons-pack/bi";
	import type { Writable } from "svelte/store";
    import { fade } from "svelte/transition";

    export let requestFunctions: {
        loadReceiptInvoices: (url: string | undefined) => Promise<void>;
        getReceiptInvoiceById: () => Promise<void>;
    }

    export let receiptInvoices: Writable<ReceiptInvoice[]>;
    // export let productSelected: Writable<ProductGet>;

    // export let toogleModalViewProductVisible: ()=>void;

    // export let idToSearch: Writable<string>;

    export let paginationData: Writable<PaginationReceiptInvonces>;

    

    // function selectProduct (e: MouseEvent) {
    //     const target = e.target as HTMLButtonElement;
    //     const td = target.parentElement as HTMLTableCellElement
    //     const tr = td.parentElement as HTMLTableRowElement
        
    //     if (tr.dataset.id) {
    //         $idToSearch = tr.dataset.id;
    //         $productSelected = { code: "", name: "", price: 0, vat_rate_id: 0 };
    //         requestFunctions.getProductById();
    //         toogleModalViewProductVisible();
    //     }
    // }
   
    function selectProduct (e: MouseEvent) {

    }

    function loadNextPage () {
        if ($paginationData.links?.next) {
            requestFunctions.loadReceiptInvoices($paginationData.links?.next)
        }
    }

    function loadPrevPage () {
        if ($paginationData.links?.prev) {
            requestFunctions.loadReceiptInvoices($paginationData.links?.prev)
        }
    }
    
    // onMount(()=>requestFunctions.loadProducts(undefined))

</script>
<section class="max-w-full">
    <!-- <h3 class="font-semibold text-lg px-3 py-1">
        Lista de productos
    </h3> -->
    <div class="block w-full  rounded-md max-w-full overflow-x-auto">
        <table class="max-w-full w-full border-collapse text-center">
            <thead class="bg-[--color-theme-1]">
                <tr>
                    <th>Fecha de emisión</th>
                    <!-- <th>Tipo de comprobante</th> -->
                    <th>Número de comprobante</th>
                    <!-- <th>Identificación</th> -->
                    <!-- <th>Razón social</th> -->
                    <th>Clave de acceso</th>
                    <!-- <th>Valor total</th> -->
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                {#each $receiptInvoices as receiptInvoice}
                <tr in:fade={{delay: ($receiptInvoices.indexOf(receiptInvoice)*50)}} class="hover:bg-slate-300" data-id="{receiptInvoice.id}">
                    <td>
                        <button class="w-full h-full p-2" on:click={(e)=>selectProduct(e)}>
                            {receiptInvoice.issuance_date}
                        </button>
                    </td>
                    <!-- <td>
                        <button class="w-full h-full p-2" on:click={(e)=>selectProduct(e)}>
                            {receiptInvoice.receipt_type}
                        </button>
                    </td> -->
                    <td>
                        <button class="w-full h-full p-2" on:click={(e)=>selectProduct(e)}>
                            {receiptInvoice.number}
                        </button>
                    </td>
                    <!-- <td>
                        <button class="w-full h-full p-2" on:click={(e)=>selectProduct(e)}>
                            {"Identificación"}
                        </button>
                    </td>
                    <td>
                        <button class="w-full h-full p-2" on:click={(e)=>selectProduct(e)}>
                            {"Razón social"}
                        </button>
                    </td> -->
                    <td>
                        <button class="w-full h-full p-2" on:click={(e)=>selectProduct(e)}>
                            {receiptInvoice.access_key}
                        </button>
                    </td>
                    <td>
                        <button class="w-full h-full p-2" on:click={(e)=>selectProduct(e)}>
                            {receiptInvoice.status}
                        </button>
                    </td>
                </tr>
                {/each}
                
            </tbody>
        </table>
    </div>
    <div class="w-full bg-[--color-theme-1] flex flex-row justify-center text-slate-50 p-1 place-items-center gap-4 rounded-b-md">
        <button class="flex flex-row gap-2 place-items-center bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600 text-slate-50" on:click={loadPrevPage}>
            <Icon src={AiFillFastBackward} size={20}/>
        </button>
        <div class="flex flex-row gap-1">
            <span>
                {#if $paginationData}
                    {#if $paginationData.meta.total > 0}
                        {$paginationData.meta.current_page}
                    {:else}
                        0
                    {/if}
                {:else}
                    0
                {/if}
            </span>
            <span>
                de
            </span>
            <span>
                {#if $paginationData}
                    {#if $paginationData.meta.total > 0}
                        {$paginationData.meta.last_page}
                    {:else}
                        0
                    {/if}
                {:else}
                    0
                {/if}
            </span>

        </div>
        <button class="flex flex-row gap-2 align-middle bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600 text-slate-50" on:click={loadNextPage}>
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
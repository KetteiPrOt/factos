<script lang="ts">
    import type { BodyRequestInvoice } from "$lib/interfaces/invoice";
    import type { Readable } from "svelte/store";
    import { slide } from "svelte/transition";

    export let sendInvoice: () => Promise<void>;
    export let loadingSendInvoice: boolean;



    

    // Request Functions 
    

</script>

<div class="flex flex-row gap-5 self-center mt-5 text-slate-50">
    <button class="h-[34px] bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={sendInvoice}>
        Firmar y enviar
    </button>
    {#if loadingSendInvoice}
        <div transition:slide={{axis: "x"}} class="spinner" role="status" aria-live="polite" aria-label="Cargando">
        </div>
    {/if}
    <button class="h-[34px] box-border border border-[--color-theme-1] text-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200">
        Guardar sin firmar
    </button>
</div>



<style>
    .spinner {
        width: 25px;
        height: 25px;
        /* min-width: 25px;
        min-height: 25px; */
        align-self: center;

        border: 5px solid white;
        border-top-color: blue;
        border-radius: 100%;

        animation: spin 1s infinite;
    }
    @keyframes spin {
        to {transform: rotate(360deg);}
    }
</style>
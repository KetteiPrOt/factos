<script lang="ts">
    import type { BodyRequestInvoice } from "$lib/interfaces/invoice";
    import type { Readable } from "svelte/store";

    export let sendInvoice: () => Promise<void>;



    

    // Request Functions 
    

</script>

<div class="flex flex-row gap-5 self-center mt-5 text-slate-50">
    <button class="h-[34px] bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={sendInvoice}>
        Firmar y enviar
    </button>
    <button class="h-[34px] box-border border border-[--color-theme-1] text-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200">
        Guardar sin firmar
    </button>
</div>
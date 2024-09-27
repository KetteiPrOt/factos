<script>
	import SectionAdquiriente from "$lib/sections/factura/SectionAdquiriente.svelte";
	import SectionBtns from "$lib/sections/factura/SectionBtns.svelte";
	import SectionCamposAdicionales from "$lib/sections/factura/SectionCamposAdicionales.svelte";
	import SectionDetalles from "$lib/sections/factura/SectionDetalles.svelte";
	import SectionFormasDePago from "$lib/sections/factura/SectionFormasDePago.svelte";
import SectionOrigen from "$lib/sections/factura/SectionOrigen.svelte";
	import SectionResumenValores from "$lib/sections/factura/SectionResumenValores.svelte";
	import { fade } from "svelte/transition";

	
</script>

<div in:fade class="flex flex-col p-5 gap-5 w-fit max-w-full h-full ">
    <h2 class="font-bold text-3xl text-center">Factura</h2>
    <div class="flex flex-col gap-7">
        <div class="flex flex-wrap gap-7 w-fit justify-center self-center">
            <SectionOrigen />
            <SectionAdquiriente />
        </div>
        <SectionDetalles />
        <div class="flex flex-wrap gap-7 w-fit justify-center max-w-full self-center">
            <div class="flex flex-col gap-7 w-fit max-w-full">
                <SectionFormasDePago />
                <SectionCamposAdicionales />
            </div>
            <SectionResumenValores />
        </div>
        <SectionBtns />
    </div>
</div>
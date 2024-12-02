<script lang="ts">
    import type { IssuancePoint } from "$lib/interfaces/issuance_point";
    import type { PaginationIssuancePonints } from "$lib/interfaces/pagination";
	import SectionAdquiriente from "$lib/sections/factura/SectionAdquiriente.svelte";
	import SectionBtns from "$lib/sections/factura/SectionBtns.svelte";
	import SectionCamposAdicionales from "$lib/sections/factura/SectionCamposAdicionales.svelte";
	import SectionDetalles from "$lib/sections/factura/SectionDetalles.svelte";
	import SectionFormasDePago from "$lib/sections/factura/SectionFormasDePago.svelte";
    import SectionOrigen from "$lib/sections/factura/SectionOrigen.svelte";
	import SectionResumenValores from "$lib/sections/factura/SectionResumenValores.svelte";
    import { onMount } from "svelte";
    import { writable, type Writable } from "svelte/store";
	import { fade } from "svelte/transition";

    let targetEstab = writable(0);
    const issuancePoints: Writable<IssuancePoint[]> = writable([]);

    // Request Functions 
    async function loadIssuancePoints (url: string | undefined = '/api/issuance-points/'+$targetEstab+'?page=1') {
        if (!$targetEstab) {return}
        const res = await fetch(url, {
            headers: {
                'Accept': 'application/json'
            },
            credentials: 'include'
        });
        
        $issuancePoints = [];
        const data: PaginationIssuancePonints = await res.json();
        if (data.data) {
            $issuancePoints = data.data;
        }
    }

    const requestFunctions = {
        loadIssuancePoints: loadIssuancePoints
    }

    onMount(loadIssuancePoints);
	
</script>

<div in:fade class="flex flex-col p-5 gap-5 w-fit max-w-full h-full ">
    <h2 class="font-bold text-3xl text-center">Factura</h2>
    <div class="flex flex-col gap-7">
        <div class="flex flex-wrap gap-7 w-fit justify-center self-center">
            <SectionOrigen targetEstab={targetEstab} requestFunctions={requestFunctions} issuancePoints={issuancePoints}/>
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
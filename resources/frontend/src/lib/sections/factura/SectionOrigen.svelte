<script lang="ts">
    import Select from "$lib/components/Select.svelte";
    import type { Establishments } from "$lib/interfaces/establishments";
    import type { Origin } from "$lib/interfaces/invoice";
    import type { IssuancePoint } from "$lib/interfaces/issuance_point";
    import type { Option } from "$lib/interfaces/select";
    import { onMount } from "svelte";
    import { writable, type Writable } from "svelte/store";

    export let requestFunctions: {
        loadIssuancePoints: (url: string | undefined) => Promise<void>;
    }
    export let bodyOrigin: Writable<Origin>;
    export let issuancePoints: Writable<IssuancePoint[]>;
    export let targetEstab: Writable<number>;
    export let selectedIssuancePoint: Writable<number> = writable(0)
    let estabs: Option[] = [];
    let issuancePointsAsOptions: Option[] = [];

    // Input Elements
    let dateInput: HTMLInputElement;

    // Update Functions
    function updateDate (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;

        $bodyOrigin.issuance_date = value;
    }

    $: {
        $bodyOrigin.establishment_id = $targetEstab;
    }

    $: {
        $bodyOrigin.issuance_point_id = $selectedIssuancePoint;
    }

    // $: {
    //     console.log($bodyOrigin)
    // }

    // Request Functions
    async function getEstabs () {
        const res = await fetch("/api/establishments", {
            headers: {
                'Accept': 'application/json'
            },
            credentials: 'include'
        })

        const data: Establishments[] = await res.json();

        if (!data) { return }
        data.forEach((estab) => {
            let opt: Option = {id: estab.id, name: estab.code + " - " + estab.address}
            let commercial_name: Option = {id: estab.id, name: estab.commercial_name}
            estabs = [...estabs, opt]
            //commercial_name_tracker = [...commercial_name_tracker, commercial_name]
        })

    };

    $: {
        if (dateInput) {
            const date = new Date();
            const minDate = new Date(date.getTime() - 90 * 24 * 60 * 60 * 1000);
            dateInput.max = date.toISOString().split('T')[0];
            dateInput.min = minDate.toISOString().split('T')[0];
        }
    }

    $: {
        issuancePointsAsOptions = [];
        $issuancePoints.forEach((ip) => {
            let newOpt: Option = {id: ip.id, name: ip.code};
            issuancePointsAsOptions = [...issuancePointsAsOptions, newOpt]
        })
    }

    $: {
        $targetEstab;
        issuancePointsAsOptions = [];
        requestFunctions.loadIssuancePoints(undefined);
    }

    $: {
        $targetEstab;
        $selectedIssuancePoint = 0;
    }

    onMount(getEstabs);

</script>

<div class="flex flex-col border border-[--color-border] p-4 rounded-lg gap-4 h-fit w-fit">
    <section class="bg-[--color-theme-1] rounded-md">
        <h3 class="font-medium text-center text-slate-50 p-1.5">Origen</h3>
    </section>
    <section class="flex flex-col gap-4">
        <div class="flex flex-row gap-5">
            <label for="establecimiento">Establecimiento:</label>
            <div class="ml-auto z-[31]">
                <Select alert={false} optionSelected={targetEstab} options={estabs} pos="down" />
            </div>
        </div>
        <div class="flex flex-row gap-5">
            <label for="fecha_emision">Fecha de emisión:</label>
            <input bind:this={dateInput} min="2024-11-25" name="fecha_emision" class="border border-[--color-border] bg-transparent rounded-md px-1 ml-auto w-[205px] place-self-center outline-none" type="date" on:change={(e)=>updateDate(e)}>
        </div>
        <div class="flex flex-row gap-5">
            <label for="punto_emision">Punto de emisión:</label>
            <div class="ml-auto">
                <Select alert={false} optionSelected={selectedIssuancePoint} options={issuancePointsAsOptions} pos="down" />
            </div>
        </div>
        <div class="flex flex-row gap-5">
            <label for="guia_remision">Guía de remision:</label>
            <input name="guia_remision" class="border border-[--color-border] bg-transparent rounded-md px-1 w-[205px] ml-auto place-self-center" type="text">
        </div>
        <div class="flex flex-row gap-6">
            <label for="factura">Factura comercial <br> negociable:</label>
            <input name="guia_remision" class="border border-[--color-border] bg-transparent rounded-md px-1 mr-auto" type="checkbox">
        </div>
    </section>
</div>
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
    export let alertsInputOrigin: Writable<{
        establishment_id: boolean, 
        issuance_date: boolean, 
        issuance_point_id: boolean
    }>;

    export let success: boolean;
    export let saving: boolean;

    let estabs: Option[] = [];
    let issuancePointsAsOptions: Option[] = [];

    // Input Elements
    let dateInput: HTMLInputElement;

    let remisionInput: HTMLInputElement;
    let chekComercial: HTMLInputElement;

    // Update Functions
    function updateDate (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;

        $bodyOrigin.issuance_date = value;
        $alertsInputOrigin.issuance_date = false;

        if (!value) {
            clearOtherInputs();
        }
    }

    $: {
        $bodyOrigin.establishment_id = $targetEstab;
        $alertsInputOrigin.establishment_id = false;
    }

    $: {
        $bodyOrigin.issuance_point_id = $selectedIssuancePoint;
        $alertsInputOrigin.issuance_point_id = false;
    }

    function clearOtherInputs () {
        if (remisionInput) {
            remisionInput.value = ""
        }
        if (chekComercial) {
            chekComercial.checked = false;
        }
    }

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

    // Clear Section
    function clearSection () {
        $targetEstab = 0;
        dateInput.value = "";
        $selectedIssuancePoint = 0;
        remisionInput.value = "";
        chekComercial.checked = false;
    }

    $: {
        if (success) {
            clearSection();
            if (typeof window !== "undefined") {
                removeData();
            }
        }
    }

    $: {
        if (saving) {
            if (typeof window !== "undefined") {
                window.localStorage.setItem("body_origin", JSON.stringify($bodyOrigin))            
            }
        }
    }

    function loadData () {
        const dataJson = window.localStorage.getItem("body_origin");
        if (dataJson !== null) {
            const data: Origin = JSON.parse(dataJson);
            //console.log(data);
            if (dateInput ){ 
                dateInput.value = data.issuance_date;
            }
            $bodyOrigin.issuance_date = data.issuance_date;
            targetEstab.set(data.establishment_id);
            $bodyOrigin.establishment_id = data.establishment_id;
            setTimeout(() => {
                selectedIssuancePoint.set(data.issuance_point_id);
                $bodyOrigin.issuance_point_id = data.issuance_point_id;
            }, 1000)
        }
    }

    function removeData () {
        window.localStorage.removeItem("body_origin");
    }

    onMount(() => {
        getEstabs();
        loadData();
    });

</script>

<div class="flex flex-col border border-[--color-border] p-4 rounded-lg gap-4 h-fit w-fit">
    <section class="bg-[--color-theme-1] rounded-md">
        <h3 class="font-medium text-center text-slate-50 p-1.5">Origen</h3>
    </section>
    <section class="flex flex-col gap-4">
        <div class="flex flex-row gap-5">
            <label for="establecimiento">Establecimiento:</label>
            <div class="ml-auto z-[31]">
                <Select alert={$alertsInputOrigin.establishment_id} optionSelected={targetEstab} options={estabs} pos="down" />
            </div>
        </div>
        <div class="flex flex-row gap-5">
            <label for="fecha_emision">Fecha de emisión:</label>
            <input bind:this={dateInput} min="2024-11-25" name="fecha_emision" class="border {$alertsInputOrigin.issuance_date ? 'border-red-500' :'border-[--color-border]'} bg-transparent rounded-md px-1 ml-auto w-[205px] place-self-center outline-none text-center" type="date" on:change={(e)=>updateDate(e)}>
        </div>
        <div class="flex flex-row gap-5">
            <label for="punto_emision">Punto de emisión:</label>
            <div class="ml-auto">
                <Select alert={$alertsInputOrigin.issuance_point_id} optionSelected={selectedIssuancePoint} options={issuancePointsAsOptions} pos="down" />
            </div>
        </div>
        <div class="flex flex-row gap-5">
            <label for="guia_remision">Guía de remision:</label>
            <input bind:this={remisionInput} name="guia_remision" class="border border-[--color-border] bg-transparent outline-none rounded-md px-1 w-[205px] ml-auto place-self-center" type="text">
        </div>
        <div class="flex flex-row gap-6">
            <label for="factura">Factura comercial <br> negociable:</label>
            <input bind:this={chekComercial} name="guia_remision" class="border border-[--color-border] bg-transparent outline-none rounded-md px-1 mr-auto" type="checkbox">
        </div>
    </section>
</div>
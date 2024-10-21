<script lang="ts">
    import Select from "$lib/components/Select.svelte";
import type { Ice } from "$lib/interfaces/ice";
    import type { IssuancePointPost } from "$lib/interfaces/issuance_point";
    import type { ProductPost } from "$lib/interfaces/product";
    import { ReceiptTypes } from "$lib/interfaces/receipt";
    import type { Vat } from "$lib/interfaces/vat";
    import { onMount } from "svelte";
    import { writable, type Writable } from "svelte/store";
	import { blur, fade, slide } from "svelte/transition";
    
    export let visible: boolean;
    export let targetEstab: Writable<number>;
    export let toogleModalNewIssuancePointVisible: ()=>void;
    export let requestFunctions: {
        loadIssuancePoints: (url: string | undefined) => Promise<void>;
        getIssuancePointById: () => Promise<void>;
    }

    let newIssuancePoint: IssuancePointPost = {
        code: 0,
        description: "",
        sequentials: {
            "1": 1,
            "2": 1,
            "3": 1,
            "4": 1,
            "5": 1,
            "6": 1,
        }
    }

    let alertsInput = {
        code: false,
        description: false,
        rt1: false,
        rt2: false,
        rt3: false,
        rt4: false,
        rt5: false,
        rt6: false,
    }

    let inputRT1: HTMLInputElement;
    let inputRT2: HTMLInputElement;
    let inputRT3: HTMLInputElement;
    let inputRT4: HTMLInputElement;
    let inputRT5: HTMLInputElement;
    let inputRT6: HTMLInputElement;

    let resMessage = {
        type: "",
        content: ""
    };

    let loading = false;

    function getCookies () {
        let cookies = document.cookie.split(";");
        let dict: {[key:string]: string} = {};
        cookies.forEach(cookie => {
            let [key, value] = cookie.split("=");
            key = key.trim();
            value = decodeURIComponent(value);
            dict[key] = value;
        })

        return dict
    }

    //Functions for newIssuancePoint
    function updateCode (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = parseInt(target.value);
        newIssuancePoint.code = value;
        alertsInput.code = false;
    }

    function updateDescription (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        newIssuancePoint.description = value;
        alertsInput.description = false;
    }

    function updateRT (e: Event, rtId: number) {
        const target = e.target as HTMLInputElement;
        const value = parseInt(target.value);
        
        newIssuancePoint.sequentials[`${rtId}` as keyof typeof newIssuancePoint.sequentials] = value;
        alertsInput[`rt${rtId}` as keyof typeof alertsInput] = false;
    }

    function getRT (rtId: number) {
        const rt: number = newIssuancePoint.sequentials[`${rtId}` as keyof typeof newIssuancePoint.sequentials];
        return rt
    }

    function clearInputs () {
        newIssuancePoint.code = 0;
        newIssuancePoint.description = '';
        newIssuancePoint.sequentials["1"] = 0;        
        newIssuancePoint.sequentials["2"] = 0;        
        newIssuancePoint.sequentials["3"] = 0;        
        newIssuancePoint.sequentials["4"] = 0;        
        newIssuancePoint.sequentials["5"] = 0;        
        newIssuancePoint.sequentials["6"] = 0;        
    }

    // Update inputsRT
    $: {
        if (newIssuancePoint.sequentials) {
            const sequentials = newIssuancePoint.sequentials
            Object.keys(sequentials).forEach(id => {
                if (inputRT1 && id === "1") {
                    inputRT1.value = sequentials["1"]+''
                }
                if (inputRT2 && id === "2") {
                    inputRT2.value = sequentials["2"]+''
                }
                if (inputRT3 && id === "3") {
                    inputRT3.value = sequentials["3"]+''
                }
                if (inputRT4 && id === "4") {
                    inputRT4.value = sequentials["4"]+''
                }
                if (inputRT5 && id === "5") {
                    inputRT5.value = sequentials["5"]+''
                }
                if (inputRT6 && id === "6") {
                    inputRT6.value = sequentials["6"]+''
                }
            })
        }
    }

    async function saveNewIssuancePoint () {
        if (loading) { return };
        let valid = true;

        if (newIssuancePoint.code < 1)  { alertsInput.code = true; valid = false };
        if (!newIssuancePoint.description)  { alertsInput.description = true; valid = false };
        if (newIssuancePoint.sequentials["1"] < 1)  { alertsInput.rt1 = true; valid = false };
        if (newIssuancePoint.sequentials["2"] < 1)  { alertsInput.rt2 = true; valid = false };
        if (newIssuancePoint.sequentials["3"] < 1)  { alertsInput.rt3 = true; valid = false };
        if (newIssuancePoint.sequentials["4"] < 1)  { alertsInput.rt4 = true; valid = false };
        if (newIssuancePoint.sequentials["5"] < 1)  { alertsInput.rt5 = true; valid = false };
        if (newIssuancePoint.sequentials["6"] < 1)  { alertsInput.rt6 = true; valid = false };



        Object.values(alertsInput).forEach(alert => {
            if (alert) {
                valid = false;
            }
        })

        if (!valid) {return}

        loading = true;
        const res = await fetch('/api/issuance-points/'+$targetEstab, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN']
            },
            method: 'POST',
            credentials: 'include',
            body: JSON.stringify(newIssuancePoint)
        })

        const data: {message: string} = await res.json();

        loading = false;

        if (res.ok) {
            clearInputs();
            requestFunctions.loadIssuancePoints(undefined);
            resMessage = {type: "success", content: "Registro exitoso"}
            
        } else if (res.status === 422) {
            resMessage = {type: "error", content: data?.message}
            if (data?.message.includes("código")) {
                alertsInput.code = true;
            } else if (data?.message.includes("descripción")) {
                alertsInput.description = true;
            } else if (data?.message.includes("secuencial #2")) {
                alertsInput.rt1 = true;
            } else if (data?.message.includes("secuencial #3")) {
                alertsInput.rt2 = true;
            } else if (data?.message.includes("secuencial #4")) {
                alertsInput.rt3 = true;
            } else if (data?.message.includes("secuencial #5")) {
                alertsInput.rt4 = true;
            } else if (data?.message.includes("secuencial #6")) {
                alertsInput.rt5 = true;
            } else if (data?.message.includes("secuencial #7")) {
                alertsInput.rt6 = true;
            }
        } else {
            resMessage = {type: "error", content: "Error al registrar el producto"}
        }
        setTimeout(() => {
            resMessage = {type: "", content: ""}
        }, 6000)
    }

    //

</script>

{#if visible}
<button transition:blur class="fixed top-0 left-0 z-50 flex flex-col bg-blue-700/60 backdrop-blur w-full h-dvh" on:click={toogleModalNewIssuancePointVisible}>
    <button class="m-auto flex flex-col w-[520px] place-items-center gap-2" on:click={(e)=>{e.stopPropagation()}}>
        <button class=" flex flex-col gap-5 p-6 border border-[--color-border] bg-slate-100/80 rounded-md hover:cursor-default" >
            <section>
                <h3 class=" font-medium text-lg">
                    Nuevo punto de emisión
                </h3>
            </section>
            <section class="flex flex-col gap-3">
                <div class="flex flex-row gap-5">
                    <label for="code">Código:</label>
                    <input name="code" class="border border-[--color-border] text-center {alertsInput.code ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="number" on:input={(e)=>updateCode(e)} value={newIssuancePoint.code}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="description">Descripción:</label>
                    <input name="description" class="border border-[--color-border] text-center {alertsInput.description ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateDescription(e)} value={newIssuancePoint.description}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="rt1">{ReceiptTypes.find((rec)=>rec.id===1)?.name}:</label>
                    <input bind:this={inputRT1} name="rt1" class="border border-[--color-border] {alertsInput.rt1 ? 'border-red-500' : ''} text-center bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="number" on:input={(e)=>updateRT(e, 1)}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="rt2" class="text-start">{ReceiptTypes.find((rec)=>rec.id===2)?.name}:</label>
                    <input bind:this={inputRT2} name="rt2" class="border border-[--color-border] {alertsInput.rt2 ? 'border-red-500' : ''} text-center bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] min-w-[205px] place-self-center" type="number" on:input={(e)=>updateRT(e, 2)}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="rt3">{ReceiptTypes.find((rec)=>rec.id===3)?.name}:</label>
                    <input bind:this={inputRT3} name="rt3" class="border border-[--color-border] {alertsInput.rt3 ? 'border-red-500' : ''} text-center bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="number" on:input={(e)=>updateRT(e, 3)}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="rt4">{ReceiptTypes.find((rec)=>rec.id===4)?.name}:</label>
                    <input bind:this={inputRT4} name="rt4" class="border border-[--color-border] {alertsInput.rt4 ? 'border-red-500' : ''} text-center bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="number" on:input={(e)=>updateRT(e, 4)}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="rt5">{ReceiptTypes.find((rec)=>rec.id===5)?.name}:</label>
                    <input bind:this={inputRT5} name="rt5" class="border border-[--color-border] {alertsInput.rt5 ? 'border-red-500' : ''} text-center bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="number" on:input={(e)=>updateRT(e, 5)}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="rt6">{ReceiptTypes.find((rec)=>rec.id===6)?.name}:</label>
                    <input bind:this={inputRT6} name="rt6" class="border border-[--color-border] {alertsInput.rt6 ? 'border-red-500' : ''} text-center bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="number" on:input={(e)=>updateRT(e, 6)}>
                </div>
            </section>
            <section class="flex flex-row gap-5 self-start text-slate-50">
                <button class="h-[34px] bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={saveNewIssuancePoint}>
                    Guardar
                </button>
                <div class="flex">
                {#if loading}
                    <div transition:slide={{axis: "x"}} class="spinner" role="status" aria-live="polite" aria-label="Cargando">
                    </div>
                {/if}
                </div>
                <button class="h-[34px] box-border border border-[--color-theme-1] text-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200" on:click={toogleModalNewIssuancePointVisible}>
                    Salir
                </button>
            </section>
        </button>
        <button class="flex h-[42px] w-full max-w-full hover:cursor-default">
            {#if resMessage.content}
            <section transition:slide class="flex mx-auto w-full max-w-full p-2 bg-slate-100/80 place-items-center place-content-center rounded-md font-bold border {resMessage.type === 'error' ? 'text-red-500 border-red-500' : 'text-lime-500 border-lime-500'}">
                <span class="max-w-full line-clamp-1">{resMessage.content}</span>
            </section>
            {/if}
        </button>
    </button>
    
</button>
{/if}


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
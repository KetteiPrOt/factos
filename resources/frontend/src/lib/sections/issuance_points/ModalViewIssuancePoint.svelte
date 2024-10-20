<script lang="ts">
    import Select from "$lib/components/Select.svelte";
import type { Ice } from "$lib/interfaces/ice";
    import type { IssuancePointGet, Sequential } from "$lib/interfaces/issuance_point";
    import type { ProductGet, ProductPost } from "$lib/interfaces/product";
    import { ReceiptTypes, type Receipt } from "$lib/interfaces/receipt";
    import type { Vat } from "$lib/interfaces/vat";
    import { list } from "postcss";
    import { onMount } from "svelte";
    import { Icon } from "svelte-icons-pack";
    import { AiOutlineClose } from "svelte-icons-pack/ai";
    import { BiTrash } from "svelte-icons-pack/bi";
    import { get, writable, type Writable } from "svelte/store";
	import { blur, fade, scale, slide } from "svelte/transition";
    
    export let visible: boolean;
    export let targetEstab: Writable<number>;
    targetEstab
    export let idToSearch: Writable<string>;
    export let issuancePointSelected: Writable<IssuancePointGet>;
    export let toogleModalViewIssuancePointVisible: ()=>void;
    export let requestFunctions: {
        loadIssuancePoints: (url: string | undefined) => Promise<void>;
        getIssuancePointById: () => Promise<void>;
    }

    let alertsInput = {
        code: false,
        description: false,
        active: false,
        rt1: false,
        rt2: false,
        rt3: false,
        rt4: false,
        rt5: false,
        rt6: false
    }

    let inputRT1: HTMLInputElement;
    let inputRT2: HTMLInputElement;
    let inputRT3: HTMLInputElement;
    let inputRT4: HTMLInputElement;
    let inputRT5: HTMLInputElement;
    let inputRT6: HTMLInputElement;

    let listRT: Sequential[] = [
        {receipt_type_id: 1, next: 0},
        {receipt_type_id: 2, next: 0},
        {receipt_type_id: 3, next: 0},
        {receipt_type_id: 4, next: 0},
        {receipt_type_id: 5, next: 0},
        {receipt_type_id: 6, next: 0},
    ]

    // Update inputsRT
    $: {
        if ($issuancePointSelected.sequentials) {
            listRT.forEach(rt => {
                let grt = getRT(rt.receipt_type_id)?.next
                if (typeof grt === 'number') {
                    rt.next =  grt;
                    if (inputRT1 && rt.receipt_type_id === 1) {
                        inputRT1.value = grt+''
                    }
                    if (inputRT2 && rt.receipt_type_id === 2) {
                        inputRT2.value = grt+''
                    }
                    if (inputRT3 && rt.receipt_type_id === 3) {
                        inputRT3.value = grt+''
                    }
                    if (inputRT4 && rt.receipt_type_id === 4) {
                        inputRT4.value = grt+''
                    }
                    if (inputRT5 && rt.receipt_type_id === 5) {
                        inputRT5.value = grt+''
                    }
                    if (inputRT6 && rt.receipt_type_id === 6) {
                        inputRT6.value = grt+''
                    }
                }
            })
        }
    }

    let resMessage = {
        type: "",
        content: ""
    };

    let loading = false;

    let confirmDeleteVisible = false;

    function toogleConfirmDeleteVisible () {
        confirmDeleteVisible = !confirmDeleteVisible;
    }

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
    

    //Functions for ViewProduct
    function updateCode (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = parseInt(target.value);
        $issuancePointSelected.code = value;
        alertsInput.code = false;
    }

    function updateDescription (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        $issuancePointSelected.description = value;
        alertsInput.description = false;
    }

    function updateActive (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.checked;
        $issuancePointSelected.active = value;
        alertsInput.active = false;
    }

    function updateRT (e: Event, rtId: number) {
        const target = e.target as HTMLInputElement;
        const value = parseInt(target.value);
        
        if (Array.isArray($issuancePointSelected.sequentials)) {
            const rt: Sequential = $issuancePointSelected.sequentials.find((seq) => {
                return seq.receipt_type_id === rtId;
            });
            
            if (typeof rt !== 'undefined') {
                rt.next = value;
                alertsInput[`rt${rtId}` as keyof typeof alertsInput] = false;
            }
        }
        
    }

    function getRT (rtId: number) {
        if (Array.isArray($issuancePointSelected.sequentials)) {
            const rt: Sequential = $issuancePointSelected.sequentials.find((seq) => {
                return seq.receipt_type_id == rtId
            })

            return rt
        }
    }

    function clearInputs () {
        $issuancePointSelected.code = 0;
        $issuancePointSelected.description = '';
        $issuancePointSelected.active = false;
        $issuancePointSelected.sequentials = [];
    }

    function checkSequentials () {
        let valid = true;
        if (Array.isArray($issuancePointSelected.sequentials)) {
            $issuancePointSelected.sequentials.forEach((seq: Sequential) => {
                if (typeof seq.next !== 'number' ) {
                    valid = false;
                    let alertTarget = 'rt'+seq.receipt_type_id
                    if (alertTarget in alertsInput) {
                        alertsInput[alertTarget as keyof typeof alertsInput] = true
                    }
                } else if (seq.next < 1) {
                    valid = false;
                    let alertTarget = 'rt'+seq.receipt_type_id
                    if (alertTarget in alertsInput) {
                        alertsInput[alertTarget as keyof typeof alertsInput] = true
                    }
                }
            })

        }
        return valid
    }

    function refinedSequentials () {
        let seqRefined = {
            '1': 0,
            '2': 0,
            '3': 0,
            '4': 0,
            '5': 0,
            '6': 0,
        };
        if (Array.isArray($issuancePointSelected.sequentials)) {
            $issuancePointSelected.sequentials.forEach((seq: Sequential) => {
                
                seqRefined[`${seq.receipt_type_id}` as keyof typeof seqRefined] = seq.next
                
            })
            $issuancePointSelected.sequentials = seqRefined;
            console.log($issuancePointSelected.code)
        }
    }

    async function saveUpdatedIssuancePoint () {
        if (loading) { return };
        let valid = true;

        if ($issuancePointSelected.code < 1)  { alertsInput.code = true; valid = false }
        if (!$issuancePointSelected.description)  { alertsInput.description = true; valid = false }
        if (!checkSequentials())  { valid = false }

        Object.values(alertsInput).forEach(alert => {
            if (alert) {
                valid = false;
            }
        })

        if (!valid) {return}

        refinedSequentials();

        loading = true;
        const res = await fetch('/api/issuance-points/'+$idToSearch, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN']
            },
            method: 'PUT',
            credentials: 'include',
            body: JSON.stringify($issuancePointSelected)
        })

        const data: {message: string} = await res.json();

        loading = false;

        if (res.ok) {
            //clearInputs();
            requestFunctions.loadIssuancePoints(undefined);
            requestFunctions.getIssuancePointById();
            resMessage = {type: "success", content: "Cambios guardados con exito"}
            
        } else if (res.status === 422) {
            resMessage = {type: "error", content: data?.message}
            if (data?.message.includes("código")) {
                alertsInput.code = true;
            } else if (data?.message.includes("descipción")) {
                alertsInput.description = true;
            } else if (data?.message.includes("secuencial #2")) {
                data.message.replace("#2", "#1");
                alertsInput.rt1 = true;
            } else if (data?.message.includes("secuencial #3")) {
                data.message.replace("#3", "#2");
                alertsInput.rt2 = true;
            } else if (data?.message.includes("secuencial #4")) {
                data.message.replace("#4", "#3");
                alertsInput.rt3 = true;
            } else if (data?.message.includes("secuencial #5")) {
                data.message.replace("#5", "#4");
                alertsInput.rt4 = true;
            } else if (data?.message.includes("secuencial #6")) {
                data.message.replace("#6", "#5");
                alertsInput.rt5 = true;
            } else if (data?.message.includes("secuencial #7")) {
                data.message.replace("#7", "#6");
                alertsInput.rt6 = true;
            }
        } else {
            resMessage = {type: "error", content: "Error al guardar los cambios"}
        }
        setTimeout(() => {
            resMessage = {type: "", content: ""}
        }, 5000)
    }

    async function deleteProduct () {
        confirmDeleteVisible = false;
        const res = await fetch('/api//'+$idToSearch, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN']
            },
            method: "DELETE",
            credentials: "include"
        })

        if (res.status === 200) {
            await requestFunctions.loadIssuancePoints(undefined);
            clearInputs();
            toogleModalViewIssuancePointVisible()
        } else {
            resMessage = {type: "error", content: "Error al guardar los cambios"}
        }
        setTimeout(() => {
            resMessage = {type: "", content: ""}
        }, 5000)
    }

    //

    


</script>

{#if visible}
<button transition:blur class="fixed top-0 left-0 z-50 flex flex-col bg-blue-700/60 backdrop-blur w-full h-dvh" on:click={()=>{toogleModalViewIssuancePointVisible(); confirmDeleteVisible = false}}>
    <button class="m-auto flex flex-col w-[520px] place-items-center gap-2" on:click={(e)=>{e.stopPropagation()}}>
        <button class=" flex flex-col gap-5 p-6 border border-[--color-border] bg-slate-100/80 rounded-md hover:cursor-default" >
            <section>
                <h3 class=" font-medium text-lg">
                    Detalles del punto de emisión
                </h3>
            </section>
            <section class="flex flex-col gap-3">
                <div class="flex flex-row gap-5">
                    <label for="code">Código:</label>
                    <input name="code" class="border border-[--color-border] text-center {alertsInput.code ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="number" on:input={(e)=>updateCode(e)} value={$issuancePointSelected.code}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="description">Descripción:</label>
                    <input name="description" class="border border-[--color-border] text-center {alertsInput.description ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateDescription(e)} value={$issuancePointSelected.description}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="is_active">Activo:</label>
                    <div class="h-[26px] w-[205px] ml-auto flex place-items-start self-center">
                        <input name="is_active" class="border border-[--color-border] bg-transparent rounded-md px-1 h-[20px] w-[20px] place-self-center" type="checkbox" on:change={(e)=>updateActive(e)} checked={$issuancePointSelected.active}>
                    </div> 
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
            <section class="flex flex-row gap-5 self-start text-slate-50 w-full">
                <button class="h-[34px] bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={saveUpdatedIssuancePoint}>
                    Guardar
                </button>
                <div class="flex">
                {#if loading}
                    <div transition:slide={{axis: "x"}} class="spinner" role="status" aria-live="polite" aria-label="Cargando">
                    </div>
                {/if}
                </div>
                <button class="h-[34px] box-border border border-[--color-theme-1] text-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200" on:click={()=>{toogleModalViewIssuancePointVisible(); confirmDeleteVisible = false}}>
                    Salir
                </button>
                <!-- <div class="ml-auto flex flex-row gap-1">
                    <button class="h-[34px] box-border border border-[--color-theme-1] text-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200" on:click={toogleConfirmDeleteVisible}>
                        {#if confirmDeleteVisible}
                        <div in:scale>
                            <Icon src={AiOutlineClose} size={20} />
                        </div>
                        {:else}    
                        <div in:scale>
                            <Icon src={BiTrash} size={20} />
                        </div>
                        {/if}
                    </button>
                    {#if confirmDeleteVisible}
                    <button transition:slide={{axis: "x"}} class="h-[34px] box-border border border-red-500 text-red-500 py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200" on:click={deleteProduct}>
                        Confirmar
                    </button>                
                    {/if}
                </div> -->
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


<style lang="postcss">
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
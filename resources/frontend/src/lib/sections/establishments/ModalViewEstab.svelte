<script lang="ts">
    import Select from "$lib/components/Select.svelte";
    import type { EstablishmentsGet } from "$lib/interfaces/establishments";
import type { Ice } from "$lib/interfaces/ice";
    import type { ProductGet, ProductPost } from "$lib/interfaces/product";
    import type { Vat } from "$lib/interfaces/vat";
    import { onMount } from "svelte";
    import { Icon } from "svelte-icons-pack";
    import { AiOutlineClose } from "svelte-icons-pack/ai";
    import { BiTrash } from "svelte-icons-pack/bi";
    import { get, writable, type Writable } from "svelte/store";
	import { blur, fade, scale, slide } from "svelte/transition";
    
    export let requestFunctions: {
        loadEstablishments: (url?: string | undefined) => Promise<void>;
        getEstablishmentsById: () => Promise<void>;
    }
    export let visible: boolean;
    export let idToSearch: Writable<string>;
    export let estabSelected: Writable<EstablishmentsGet>;
    export let toogleModalViewEstabVisible: ()=>void;

    let alertsInput = {
        code: false,
        address: false,
        commercial_name: false,
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
        $estabSelected.code = value;
        alertsInput.code = false;
    }

    function updateAddress (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        $estabSelected.address = value;
        alertsInput.address = false;
    }

    function updateCommercialName (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        $estabSelected.commercial_name = value;
        alertsInput.commercial_name = false;
    } 

    function clearInputs () {
        $estabSelected.code = 0;
        $estabSelected.address = '';
        $estabSelected.commercial_name = '';
 
    }

    async function saveUpdatedEstablishment () {
        if (loading) { return };
        let valid = true;

        if (!$estabSelected.code)  { alertsInput.code = true; valid = false }
        if (!$estabSelected.address)  { alertsInput.address = true; valid = false }
        if (!$estabSelected.commercial_name)  { alertsInput.commercial_name = true; valid = false }
        
        Object.values(alertsInput).forEach(alert => {
            if (alert) {
                valid = false;
            }
        })

        if (!valid) {return}

        loading = true;
        const res = await fetch('/api/establishments/'+$idToSearch, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN']
            },
            method: 'PUT',
            credentials: 'include',
            body: JSON.stringify($estabSelected)
        })

        const data: {message: string} = await res.json();

        loading = false;

        if (res.ok) {
            //clearInputs();
            requestFunctions.loadEstablishments();
            requestFunctions.getEstablishmentsById();
            resMessage = {type: "success", content: "Cambios guardados con exito"}
            
        } else if (res.status === 422) {
            resMessage = {type: "error", content: data?.message}
            if (data?.message.includes("código")) {
                alertsInput.code = true;
            } else if (data?.message.includes("dirección")) {
                alertsInput.address = true;
            } else if (data?.message.includes("nombre")) {
                alertsInput.commercial_name = true;
            }
        } else {
            resMessage = {type: "error", content: "Error al guardar los cambios"}
        }
        setTimeout(() => {
            resMessage = {type: "", content: ""}
        }, 5000)
    }

    async function deleteEstablishments () {
        confirmDeleteVisible = false;
        const res = await fetch('/api/establishments/'+$idToSearch, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN']
            },
            method: "DELETE",
            credentials: "include"
        })

        if (res.status === 200) {
            await requestFunctions.loadEstablishments();
            clearInputs();
            toogleModalViewEstabVisible()
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
<button transition:blur class="fixed top-0 left-0 z-50 flex flex-col bg-blue-700/60 backdrop-blur w-full h-dvh" on:click={()=>{toogleModalViewEstabVisible(); confirmDeleteVisible = false}}>
    <button class="m-auto flex flex-col w-[409px] place-items-center gap-2" on:click={(e)=>{e.stopPropagation()}}>
        <button class=" flex flex-col gap-5 p-6 border border-[--color-border] bg-slate-100/80 rounded-md hover:cursor-default" >
            <section>
                <h3 class=" font-medium text-lg">
                    Detalles del establecimiento
                </h3>
            </section>
            <section class="flex flex-col gap-3">
                <div class="flex flex-row gap-5">
                    <label for="code">Código:</label>
                    <input name="code" class="border border-[--color-border] text-center {alertsInput.code ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="number" on:input={(e)=>updateCode(e)} value={$estabSelected.code ? $estabSelected.code : ''}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="address">Dirección:</label>
                    <input name="address" class="border border-[--color-border] text-center {alertsInput.address ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateAddress(e)} value={$estabSelected.address}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="commercial_name" class="text-start">Nombre comercial:</label>
                    <input name="commercial_name" class="border border-[--color-border] text-center {alertsInput.commercial_name ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateCommercialName(e)} value={$estabSelected.commercial_name}>
                </div>
            </section>
            <section class="flex flex-row gap-5 self-start text-slate-50 w-full">
                <button class="h-[34px] bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={saveUpdatedEstablishment}>
                    Guardar
                </button>
                <div class="flex">
                {#if loading}
                    <div transition:slide={{axis: "x"}} class="spinner" role="status" aria-live="polite" aria-label="Cargando">
                    </div>
                {/if}
                </div>
                <button class="h-[34px] box-border border border-[--color-theme-1] text-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200" on:click={()=>{toogleModalViewEstabVisible(); confirmDeleteVisible = false}}>
                    Salir
                </button>
                <div class="ml-auto flex flex-row gap-1">
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
                    <button transition:slide={{axis: "x"}} class="h-[34px] box-border border border-red-500 text-red-500 py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200" on:click={deleteEstablishments}>
                        Confirmar
                    </button>                
                    {/if}
                </div>
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
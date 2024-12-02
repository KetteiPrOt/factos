<script lang="ts">
    import Select from "$lib/components/Select.svelte";
import type { Ice } from "$lib/interfaces/ice";
    import type { ProductPost } from "$lib/interfaces/product";
    import type { Vat } from "$lib/interfaces/vat";
    import { onMount } from "svelte";
    import { writable } from "svelte/store";
	import { blur, fade, slide } from "svelte/transition";
    
    export let requestFunctions: {
        loadProducts: () => Promise<void>;
    }
    export let visible: boolean;
    export let toogleModalNewProductVisible: ()=>void;


    let listVat: Vat[] = [];
    let listIce: Ice[] = [];

    export const iceIdSelected = writable(0);
    export const vatIdSelected = writable(0);

    let newProduct: ProductPost = {
        code: "",
        name: "",
        price: 0,
        vat_rate_id: 0 
    }

    let alertsInput = {
        code: false,
        name: false,
        price: false,
        vat_rate_id: false,
        ice_type_id: false
    }

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
    
    async function getDefaultParams () {
        const resVat = await fetch('/api/vat-rates');
        const resIce = await fetch('/api/ice-types');

        const dataVat = await resVat.json();
        const dataIce = await resIce.json();


        listVat = dataVat;
        listIce = dataIce;
    }

    //Functions for newProduct
    function updateCode (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        newProduct.code = value;
        alertsInput.code = false;
    }

    function updateName (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        newProduct.name = value;
        alertsInput.name = false;
    }

    function updatePrice (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = parseFloat(target.value);
        newProduct.price = value;
        alertsInput.price = false;
    }

    function updateInfoAdicional (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        newProduct.additional_info = value;
        if (newProduct.additional_info === "") {
            delete newProduct.additional_info;
        }
    }

    // Updated VAT ID
    $: {
        newProduct.vat_rate_id = $vatIdSelected;
        alertsInput.vat_rate_id = false;
    }

    
    // Update ICE ID
    $: {
        newProduct.ice_type_id = $iceIdSelected != 0 ? $iceIdSelected : undefined;
        alertsInput.ice_type_id = false;
        if (!newProduct.ice_type_id) {
            delete newProduct.ice_type_id;
        }
    }

    function toogleIceApplies (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.checked;
        newProduct.ice_applies = value;
        alertsInput.ice_type_id = false;
        if (newProduct.ice_applies === false) {
            delete newProduct.ice_applies;
            $iceIdSelected = 0
        }
    } 


    function toogleTourismIvaApplies (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.checked;
        newProduct.tourism_vat_applies = value;
        if (newProduct.tourism_vat_applies === false) {
            delete newProduct.tourism_vat_applies;
        }
    } 

    function clearInputs () {
        newProduct.code = '';
        newProduct.name = '';
        newProduct.price = 0;
        $vatIdSelected = 0;
        if ('additional_info' in newProduct) {
            delete newProduct.additional_info;
        }
        if ('tourism_vat_applies' in newProduct) {
            delete newProduct.tourism_vat_applies;
        }
        if ('ice_applies' in newProduct) {
            delete newProduct.ice_applies;
        }
        $iceIdSelected = 0
    }

    async function saveNewProduct () {
        if (loading) { return };
        let valid = true;

        if (!newProduct.code)  { alertsInput.code = true; valid = false }
        if (!newProduct.name)  { alertsInput.name = true; valid = false }
        if (!newProduct.price)  { alertsInput.price = true; valid = false }
        if (!newProduct.vat_rate_id)  { alertsInput.vat_rate_id = true; valid = false }
        if (!newProduct.ice_type_id && newProduct.ice_applies)  { alertsInput.ice_type_id = true; valid = false }

        Object.values(alertsInput).forEach(alert => {
            if (alert) {
                valid = false;
            }
        })

        if (!valid) {return}

        loading = true;
        const res = await fetch('/api/products', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN']
            },
            method: 'POST',
            credentials: 'include',
            body: JSON.stringify(newProduct)
        })

        const data: {message: string} = await res.json();

        loading = false;

        if (res.ok) {
            clearInputs();
            requestFunctions.loadProducts();
            resMessage = {type: "success", content: "Registro exitoso"}
            
        } else if (res.status === 422) {
            resMessage = {type: "error", content: data?.message}
            if (data?.message.includes("código")) {
                alertsInput.code = true;
            } else if (data?.message.includes("precio")) {
                alertsInput.price = true;
            }
        } else {
            resMessage = {type: "error", content: "Error al registrar el producto"}
        }
        setTimeout(() => {
            resMessage = {type: "", content: ""}
        }, 6000)
    }

    //

    onMount(getDefaultParams);

</script>

{#if visible}
<button transition:blur class="fixed top-0 left-0 z-50 flex flex-col bg-blue-700/60 backdrop-blur w-full h-dvh" on:click={toogleModalNewProductVisible}>
    <button class="m-auto flex flex-col w-[409px] place-items-center gap-2" on:click={(e)=>{e.stopPropagation()}}>
        <button class=" flex flex-col gap-5 p-6 border border-[--color-border] bg-slate-100/80 rounded-md hover:cursor-default" >
            <section>
                <h3 class=" font-medium text-lg">
                    Nuevo producto
                </h3>
            </section>
            <section class="flex flex-col gap-3">
                <div class="flex flex-row gap-5">
                    <label for="code">Código:</label>
                    <input name="code" class="border border-[--color-border] text-center {alertsInput.code ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateCode(e)} value={newProduct.code}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="name">Nombre:</label>
                    <input name="name" class="border border-[--color-border] text-center {alertsInput.name ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateName(e)} value={newProduct.name}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="price">Precio:</label>
                    <input name="price" class="border border-[--color-border] text-center {alertsInput.price ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="number" on:input={(e)=>updatePrice(e)} value={newProduct.price ? newProduct.price : ''}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="additional_info">Info. Adicional:</label>
                    <input name="additional_info" class="border border-[--color-border] text-center bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateInfoAdicional(e)} value={newProduct.additional_info ? newProduct.additional_info : ''}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="vat_rate_id">Tarifa IVA:</label>
                    <Select options={listVat} optionSelected={vatIdSelected} alert={alertsInput.vat_rate_id} />
                </div>
                <div class="flex flex-row gap-5">
                    <label for="tourism_vat_applies">Aplicar IVA Turista:</label>
                    <div class="h-[26px] w-[205px] ml-auto flex place-items-start self-center">
                        <input name="tourism_vat_applies" class="border border-[--color-border] bg-transparent rounded-md px-1 h-[20px] w-[20px] place-self-center" type="checkbox" on:change={(e)=>toogleTourismIvaApplies(e)} checked={newProduct.tourism_vat_applies}>
                    </div> 
                </div>
                <div class="flex flex-row gap-5">
                    <label for="ice_applies">Aplicar ICE:</label>
                    <div class="h-[26px] w-[205px] ml-auto flex place-items-start self-center">
                        <input name="ice_applies" class="border border-[--color-border] bg-transparent rounded-md px-1 h-[20px] w-[20px] place-self-center" type="checkbox" on:change={(e)=>toogleIceApplies(e)} checked={newProduct.ice_applies}>
                    </div> 
                </div>
                {#if newProduct?.ice_applies}
                <div transition:slide={{duration: 200}} class="flex flex-row gap-5">
                    <label for="ice_type_id">Tipo ICE:</label>
                    <Select options={listIce} optionSelected={iceIdSelected} alert={alertsInput.ice_type_id}/>
                </div>
                {/if}
            </section>
            <section class="flex flex-row gap-5 self-start text-slate-50">
                <button class="h-[34px] bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={saveNewProduct}>
                    Guardar
                </button>
                <div class="flex">
                {#if loading}
                    <div transition:slide={{axis: "x"}} class="spinner" role="status" aria-live="polite" aria-label="Cargando">
                    </div>
                {/if}
                </div>
                <button class="h-[34px] box-border border border-[--color-theme-1] text-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200" on:click={toogleModalNewProductVisible}>
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
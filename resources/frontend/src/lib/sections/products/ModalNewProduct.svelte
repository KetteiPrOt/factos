<script lang="ts">
    import { url_api } from "$lib/global_stores/config";
    import type { Ice } from "$lib/interfaces/ice";
    import type { ProductPost } from "$lib/interfaces/product";
    import type { Vat } from "$lib/interfaces/vat";
    import { onMount } from "svelte";
	import { blur, fade, slide } from "svelte/transition";
    
    
    export let visible: boolean;
    export let toogleModalVisible: ()=>void;

    let listVat: Vat[] = [];
    let listIce: Ice[] = [];

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
        const resVat = await fetch(`${$url_api}/vat-rates`);
        const resIce = await fetch(`${$url_api}/ice-types`);

        const dataVat = await resVat.json();
        const dataIce = await resIce.json();


        listVat = dataVat;
        listIce = dataIce;

        console.log(listVat);
        console.log(listIce)
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

    function updateVatId (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = parseInt(target.value);
        newProduct.vat_rate_id = value;
        alertsInput.vat_rate_id = false;
    }

    function updateIceId (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = parseInt(target.value);
        newProduct.ice_type_id = value;
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
            if (newProduct.ice_type_id) {
                delete newProduct.ice_type_id;
            }
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

    async function saveNewProduct () {
        let valid = true;

        if (!newProduct.code)  { alertsInput.code = true; valid = false }
        if (!newProduct.name)  { alertsInput.name = true; valid = false }
        if (!newProduct.price)  { alertsInput.price = true; valid = false }
        if (!newProduct.vat_rate_id)  { alertsInput.vat_rate_id = true; valid = false }
        if (!newProduct.ice_type_id && newProduct.ice_applies)  { alertsInput.ice_type_id = true; valid = false }

        if (!valid) {return}

        console.log(newProduct)
        const res = await fetch(`https://factos.test/api/products`, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN']
            },
            method: 'POST',
            credentials: 'include',
            body: JSON.stringify(newProduct)
        })
    }

    onMount(getDefaultParams);
    onMount(async () => {
        fetch('https://factos.test/sanctum/csrf-cookie', {headers: {'Accept': 'application/json', 'Origin': 'http://localhost:5173', 'Referer': 'http://localhost:5173/products'}, credentials: 'include'})
        .then(res => console.log(res));
    })

</script>

{#if visible}
<button transition:blur class="fixed top-0 left-0 flex bg-blue-700/60 backdrop-blur w-full h-dvh" on:click={toogleModalVisible}>
    <button class="m-auto flex flex-col gap-5 p-6 border border-[--color-border] bg-slate-100/80 rounded-md hover:cursor-default" on:click={(e)=>{e.stopPropagation()}}>
        <section>
            <h3 class=" font-medium text-lg">
                Nuevo producto
            </h3>
        </section>
        <section class="flex flex-col gap-3">
            <div class="flex flex-row gap-5">
                <label for="code">Código:</label>
                <input name="code" class="border border-[--color-border] {alertsInput.code ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateCode(e)}>
            </div>
            <div class="flex flex-row gap-5">
                <label for="name">Nombre:</label>
                <input name="name" class="border border-[--color-border] {alertsInput.name ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateName(e)}>
            </div>
            <div class="flex flex-row gap-5">
                <label for="price">Precio:</label>
                <input name="price" class="border border-[--color-border] {alertsInput.price ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="number" on:input={(e)=>updatePrice(e)}>
            </div>
            <div class="flex flex-row gap-5">
                <label for="additional_info">Info. Adicional:</label>
                <input name="additional_info" class="border border-[--color-border] bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateInfoAdicional(e)}>
            </div>
            <div class="flex flex-row gap-5">
                <label for="vat_rate_id">Tarifa IVA:</label>
                <select name="vat_rate_id" class="border border-[--color-border] {alertsInput.vat_rate_id ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center outline-none" on:change={e=>updateVatId(e)}>
                    <option value=""></option>
                    {#each listVat as vat}
                        <option value={vat.id}>{vat.name}</option>
                    {/each}
                </select>
            </div>
            <div class="flex flex-row gap-5">
                <label for="tourism_vat_applies">Aplicar IVA Turista:</label>
                <div class="h-[26px] w-[205px] ml-auto flex place-items-start self-center">
                    <input name="tourism_vat_applies" class="border border-[--color-border] bg-transparent rounded-md px-1 h-[20px] w-[20px] place-self-center" type="checkbox" on:change={(e)=>toogleTourismIvaApplies(e)}>
                </div> 
            </div>
            <div class="flex flex-row gap-5">
                <label for="ice_applies">Aplicar ICE:</label>
                <div class="h-[26px] w-[205px] ml-auto flex place-items-start self-center">
                    <input name="ice_applies" class="border border-[--color-border] bg-transparent rounded-md px-1 h-[20px] w-[20px] place-self-center" type="checkbox" on:change={(e)=>toogleIceApplies(e)}>
                </div> 
            </div>
            {#if newProduct?.ice_applies}
            <div transition:slide={{duration: 200}} class="flex flex-row gap-5">
                <label for="ice_type_id">Tipo ICE:</label>
                <select name="ice_type_id" class="border border-[--color-border] {alertsInput.ice_type_id ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center outline-none" on:change={(e)=>updateIceId(e)}>
                    <option value=""></option>
                    {#each listIce as ice}
                        <option value={ice.id}>{ice.name}</option>
                    {/each}
                </select>
            </div>
            {/if}
        </section>
        <section class="flex flex-row gap-5 self-start text-slate-50">
            <button class="h-[34px] bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={saveNewProduct}>
                Guardar
            </button>
            <button class="h-[34px] box-border border border-[--color-theme-1] text-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200" on:click={toogleModalVisible}>
                Cancelar
            </button>
        </section>
    </button>
</button>
{/if}
<script lang="ts">
    import Select from "$lib/components/Select.svelte";
    import type { Ice } from "$lib/interfaces/ice";
    import { IdentificationTypesExcludeFinalConsumer } from "$lib/interfaces/identification";
    import type { PersonGet } from "$lib/interfaces/person";
    import type { ProductGet, ProductPost } from "$lib/interfaces/product";
    import type { Vat } from "$lib/interfaces/vat";
    import { onMount } from "svelte";
    import { Icon } from "svelte-icons-pack";
    import { AiOutlineClose } from "svelte-icons-pack/ai";
    import { BiTrash } from "svelte-icons-pack/bi";
    import { get, writable, type Writable } from "svelte/store";
	import { blur, fade, scale, slide } from "svelte/transition";
    
    export let requestFunctions: {
        loadPersons: (url?: string | undefined) => Promise<void>;
        getPersonById: () => Promise<void>;
    }
    export let visible: boolean;
    export let idToSearch: Writable<string>;
    export let personSelected: Writable<PersonGet>;
    export let toogleModalViewPersonVisible: ()=>void;

    let identificationTypeIdSelected = writable(0);

    let alertsInput = {
        identification: false,
        social_reason: false,
        email: false,
        phone_number: false,
        identification_type_id: false
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

    //Functions for ViewPerson
    function updateIdentification (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        $personSelected.identification = value;
        alertsInput.identification = false;
    }

    function updateSocialReason (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        $personSelected.social_reason = value;
        alertsInput.social_reason = false;
    }

    function updateEmail (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        $personSelected.email = value;
        alertsInput.email = false;
    }

    function updatePhoneNumber (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        $personSelected.phone_number = value;
        alertsInput.phone_number = false;
    }

    function updateAddress (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        $personSelected.address = value;
    }

    // Update Identification Type ID
    $: {
        if ($personSelected?.new) {
            let initIndentTypeId = get(personSelected).identification_type_id;
            if (typeof initIndentTypeId != 'undefined') {
                identificationTypeIdSelected.set(initIndentTypeId);
            } else {
                identificationTypeIdSelected.set(0);
            }
            delete $personSelected.new;
        }
        $personSelected.identification_type_id = $identificationTypeIdSelected
        alertsInput.identification_type_id = false;
    } 

    function clearInputs () {
        $personSelected.identification = '';
        $personSelected.social_reason = '';
        $personSelected.email = "";
        $personSelected.phone_number = "";
        $personSelected.address = "";
        $identificationTypeIdSelected = 0;
    }

    function checkIdentification () {
        let valid = true;
        const identLength = $personSelected.identification.length
        if (identLength === 10 || identLength === 13) {
            Array.from($personSelected.identification).forEach((char) => {
                if (isNaN(parseInt(char))) {
                    valid = false;
                }
            })
        } else {
            valid = false;
        }

        return valid;
    }

    function checkPhoneNumber () {
        let valid = true;
        if ($personSelected.phone_number) {
            const phoneLength = $personSelected.phone_number.length;
            if (phoneLength === 10) {
                Array.from($personSelected.phone_number).forEach((char) => {
                    if (isNaN(parseInt(char))) {
                        valid = false;
                    } 
                })
            } else {
                valid = false;
            }
        }

        return valid
    }

    const emailRegex = /^[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,}$/;

    async function saveUpdatedPerson () {
        if (loading) { return };
        let valid = true;

        if (!checkIdentification())  { alertsInput.identification = true; valid = false }
        if (!$personSelected.social_reason)  { alertsInput.social_reason = true; valid = false }
        if (!emailRegex.test($personSelected.email))  { alertsInput.email = true; valid = false }
        if (!checkPhoneNumber())  { alertsInput.phone_number = true; valid = false }
        if (!$personSelected.identification_type_id)  { alertsInput.identification_type_id = true; valid = false }

        if ($personSelected.phone_number === '') {
            $personSelected.phone_number = null;
        }
        if ($personSelected.address === '') {
            $personSelected.address = null;
        }

        Object.values(alertsInput).forEach(alert => {
            if (alert) {
                valid = false;
            }
        })

        if (!valid) {return}

        loading = true;
        const res = await fetch('/api/persons/'+$idToSearch, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN']
            },
            method: 'PUT',
            credentials: 'include',
            body: JSON.stringify($personSelected)
        })

        const data: {message: string} = await res.json();

        loading = false;

        if (res.ok) {
            //clearInputs();
            requestFunctions.loadPersons();
            requestFunctions.getPersonById();
            resMessage = {type: "success", content: "Cambios guardados con exito"}
            
        } else if (res.status === 422) {
            resMessage = {type: "error", content: data?.message}
            if (data?.message.includes("identificación")) {
                alertsInput.identification = true;
            } else if (data?.message.includes("razón")) {
                alertsInput.social_reason = true;
            } else if (data?.message.includes("correo")) {
                alertsInput.email = true;
            } else if (data?.message.includes("tipo")) {
                alertsInput.identification_type_id = true;
            }
        } else {
            resMessage = {type: "error", content: "Error al guardar los cambios"}
        }
        setTimeout(() => {
            resMessage = {type: "", content: ""}
        }, 5000)
    }

    async function deletePerson () {
        confirmDeleteVisible = false;
        const res = await fetch('/api/persons/'+$idToSearch, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN']
            },
            method: "DELETE",
            credentials: "include"
        })

        if (res.status === 200) {
            await requestFunctions.loadPersons();
            clearInputs();
            toogleModalViewPersonVisible()
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
<button transition:blur class="fixed top-0 left-0 z-50 flex flex-col bg-blue-700/60 backdrop-blur w-full h-dvh" on:click={()=>{toogleModalViewPersonVisible(); confirmDeleteVisible = false}}>
    <button class="m-auto flex flex-col w-[409px] place-items-center gap-2" on:click={(e)=>{e.stopPropagation()}}>
        <button class=" flex flex-col gap-5 p-6 border border-[--color-border] bg-slate-100/80 rounded-md hover:cursor-default" >
            <section>
                <h3 class=" font-medium text-lg">
                    Detalles del cliente
                </h3>
            </section>
            <section class="flex flex-col gap-3">
                <div class="flex flex-row gap-5">
                    <label for="identification">Identificación:</label>
                    <input name="identification" class="border border-[--color-border] text-center {alertsInput.identification ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateIdentification(e)} value={$personSelected.identification}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="social_reason">Razón social:</label>
                    <input name="social_reason" class="border border-[--color-border] text-center {alertsInput.social_reason ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateSocialReason(e)} value={$personSelected.social_reason}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="email">Correo:</label>
                    <input name="email" class="border border-[--color-border] text-center {alertsInput.email ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateEmail(e)} value={$personSelected.email}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="phone_number">Teléfono:</label>
                    <input name="phone_number" class="border border-[--color-border] {alertsInput.phone_number ? 'border-red-500' : ''} text-center bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updatePhoneNumber(e)} value={$personSelected.phone_number ? $personSelected.phone_number : ''}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="address">Dirección:</label>
                    <input name="address" class="border border-[--color-border] text-center bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateAddress(e)} value={$personSelected.address ? $personSelected.address : ''}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="identification_type_id" class="text-start">Tipo de identificación:</label>
                    <div class="flex min-w-[205px] align-middle">
                        <Select options={IdentificationTypesExcludeFinalConsumer} optionSelected={identificationTypeIdSelected} alert={alertsInput.identification_type_id} />
                    </div>
                </div>
            </section>
            <section class="flex flex-row gap-5 self-start text-slate-50 w-full">
                <button class="h-[34px] bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={saveUpdatedPerson}>
                    Guardar
                </button>
                <div class="flex">
                {#if loading}
                    <div transition:slide={{axis: "x"}} class="spinner" role="status" aria-live="polite" aria-label="Cargando">
                    </div>
                {/if}
                </div>
                <button class="h-[34px] box-border border border-[--color-theme-1] text-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200" on:click={()=>{toogleModalViewPersonVisible(); confirmDeleteVisible = false}}>
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
                    <button transition:slide={{axis: "x"}} class="h-[34px] box-border border border-red-500 text-red-500 py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200" on:click={deletePerson}>
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
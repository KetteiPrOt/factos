<script lang="ts">
    import Select from "$lib/components/Select.svelte";
    import { IdentificationTypesExcludeFinalConsumer } from "$lib/interfaces/identification";
    import type { PersonPost } from "$lib/interfaces/person";
    import { writable } from "svelte/store";
	import { blur, slide } from "svelte/transition";
    
    export let requestFunctions: {
        loadPersons: (url?: string | undefined) => Promise<void>;
        getPersonById: () => Promise<void>;
    }
    export let visible: boolean;
    export let toogleModalNewPersonVisible: ()=>void;

    let newPerson: PersonPost = {
        identification: "",
        social_reason: "",
        email: "",
        identification_type_id: 0
    }

    const identificationTypeIdSelected = writable(0);
    
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

    //Functions for newPerson
    function updateIdentification (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        newPerson.identification = value;
        alertsInput.identification = false;
    }

    function updateSocialReason (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        newPerson.social_reason = value;
        alertsInput.social_reason = false;
    }

    function updateEmail (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        newPerson.email = value;
        alertsInput.email = false;
    }

    function updatePhoneNumber (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        newPerson.phone_number = value;
        alertsInput.phone_number = false;
    }

    function updateAddress (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        newPerson.address = value;
    }

    // Updated Identification Type ID
    $: {
        newPerson.identification_type_id = $identificationTypeIdSelected
        alertsInput.identification_type_id = false;
    }

    function clearInputs () {
        newPerson.identification = '';
        newPerson.social_reason = '';
        newPerson.email = "";
        newPerson.phone_number = "";
        newPerson.address = "";
        $identificationTypeIdSelected = 0;
    }

    function checkIdentification () {
        let valid = true;
        const identLength = newPerson.identification.length
        if (identLength === 10 || identLength === 13) {
            Array.from(newPerson.identification).forEach((char) => {
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
        if (newPerson.phone_number) {
            const phoneLength = newPerson.phone_number.length;
            if (phoneLength === 10) {
                Array.from(newPerson.phone_number).forEach((char) => {
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

    async function saveNewPerson () {
        if (loading) { return };
        let valid = true;

        if (!checkIdentification())  { alertsInput.identification = true; valid = false }
        if (!newPerson.social_reason)  { alertsInput.social_reason = true; valid = false }
        if (!emailRegex.test(newPerson.email))  { alertsInput.email = true; valid = false }
        if (!checkPhoneNumber())  { alertsInput.phone_number = true; valid = false }
        if (!newPerson.identification_type_id)  { alertsInput.identification_type_id = true; valid = false }

        if (newPerson.phone_number === '') {
            delete newPerson.phone_number;
        }
        if (newPerson.address === '') {
            delete newPerson.address;
        }

        Object.values(alertsInput).forEach(alert => {
            if (alert) {
                valid = false;
            }
        })

        if (!valid) {return}

        loading = true;
        const res = await fetch('/api/persons', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN']
            },
            method: 'POST',
            credentials: 'include',
            body: JSON.stringify(newPerson)
        })

        const data: {message: string} = await res.json();

        loading = false;

        if (res.ok) {
            clearInputs();
            requestFunctions.loadPersons();
            resMessage = {type: "success", content: "Registro exitoso"}
            
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
            resMessage = {type: "error", content: "Error al registrar el cliente"}
        }
        setTimeout(() => {
            resMessage = {type: "", content: ""}
        }, 6000)
    }

    //

</script>

{#if visible}
<button transition:blur class="fixed top-0 left-0 z-50 flex flex-col bg-blue-700/60 backdrop-blur w-full h-dvh" on:click={toogleModalNewPersonVisible}>
    <button class="m-auto flex flex-col w-[409px] place-items-center gap-2" on:click={(e)=>{e.stopPropagation()}}>
        <button class=" flex flex-col gap-5 p-6 border border-[--color-border] bg-slate-100/80 rounded-md hover:cursor-default" >
            <section>
                <h3 class=" font-medium text-lg">
                    Nuevo cliente
                </h3>
            </section>
            <section class="flex flex-col gap-3">
                <div class="flex flex-row gap-5">
                    <label for="identification">Identificación:</label>
                    <input name="identification" class="border border-[--color-border] text-center {alertsInput.identification ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateIdentification(e)} value={newPerson.identification}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="social_reason">Razón social:</label>
                    <input name="social_reason" class="border border-[--color-border] text-center {alertsInput.social_reason ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateSocialReason(e)} value={newPerson.social_reason}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="email">Correo:</label>
                    <input name="email" class="border border-[--color-border] text-center {alertsInput.email ? 'border-red-500' : ''} bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateEmail(e)} value={newPerson.email}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="phone_number">Teléfono:</label>
                    <input name="phone_number" class="border border-[--color-border] {alertsInput.phone_number ? 'border-red-500' : ''} text-center bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updatePhoneNumber(e)} value={newPerson.phone_number ? newPerson.phone_number : ''}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="address">Dirección:</label>
                    <input name="address" class="border border-[--color-border] text-center bg-transparent rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={(e)=>updateAddress(e)} value={newPerson.address ? newPerson.address : ''}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="identification_type_id" class="text-start">Tipo de identificación:</label>
                    <div class="flex min-w-[205px] align-middle">
                        <Select options={IdentificationTypesExcludeFinalConsumer} optionSelected={identificationTypeIdSelected} alert={alertsInput.identification_type_id} />
                    </div>
                </div>
            </section>
            <section class="flex flex-row gap-5 self-start text-slate-50">
                <button class="h-[34px] bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={saveNewPerson}>
                    Guardar
                </button>
                <div class="flex">
                {#if loading}
                    <div transition:slide={{axis: "x"}} class="spinner" role="status" aria-live="polite" aria-label="Cargando">
                    </div>
                {/if}
                </div>
                <button class="h-[34px] box-border border border-[--color-theme-1] text-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200" on:click={toogleModalNewPersonVisible}>
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
<script lang="ts">
    import Select from "$lib/components/Select.svelte";
    import { IdentificationTypes } from "$lib/interfaces/identification";
    import type { Acquirer } from "$lib/interfaces/invoice";
    import { onMount } from "svelte";
    import type { HTMLInputAttributes } from "svelte/elements";
    import { writable, type Writable } from "svelte/store";

    

    export let bodyAcquirer: Writable<Acquirer>;
    export let alertsInputAcquirer: Writable<{
        identification_type_id: boolean;
        identification: boolean;
        social_reason: boolean;
        phone_number: boolean;
        address: boolean;
        email: boolean;
    }>;

    export let success: boolean;
    export let saving: boolean;

    let selectedIdentificationTypeId = writable(0);

    // Input Elements
    let inputIdentification: HTMLInputElement;
    let inputSocialReason: HTMLInputElement;
    let inputPhoneNumber: HTMLInputElement;
    let inputAddress: HTMLInputElement;
    let inputEmail: HTMLInputElement;


    $: {
        $bodyAcquirer.identification_type_id = $selectedIdentificationTypeId
        $alertsInputAcquirer.identification_type_id = false;
    }

    $: {
        if ($selectedIdentificationTypeId === 4) {
            if (inputIdentification) {
                $bodyAcquirer.identification = "";
                inputIdentification.disabled = true;
            }
            if (inputSocialReason) {
                $bodyAcquirer.social_reason = ""
                inputSocialReason.disabled = true;
            }
            if (inputPhoneNumber) {
                $bodyAcquirer.phone_number = ""
                inputPhoneNumber.disabled = true;
            }
            if (inputAddress) {
                $bodyAcquirer.address = ""
                inputAddress.disabled = true;
            }
            if (inputEmail) {
                $bodyAcquirer.email = ""
                inputEmail.disabled = true;
            }
        } else {
            if (inputIdentification) {
                $bodyAcquirer.identification = "";
                inputIdentification.disabled = false;
            }
            if (inputSocialReason) {
                $bodyAcquirer.social_reason = ""
                inputSocialReason.disabled = false;
            }
            if (inputPhoneNumber) {
                $bodyAcquirer.phone_number = ""
                inputPhoneNumber.disabled = false;
            }
            if (inputAddress) {
                $bodyAcquirer.address = ""
                inputAddress.disabled = false;
            }
            if (inputEmail) {
                $bodyAcquirer.email = ""
                inputEmail.disabled = false;
            }
            
        }
    }

    // $: {
    //     console.log($bodyAcquirer)
    // }

    // Update functions
    // function updatePhoneNumber (e: Event | InputEvent) {
    //     const target = e.target as HTMLInputElement;
    //     if (e instanceof InputEvent) {
    //         if (e.data !== null) {
    //             if (isNaN(parseInt(e.data))) {
    //                 valid = false;
    //             }
    //         }
    //     }
    // }

    // Clear Section
    function clearSection () {
        $selectedIdentificationTypeId = 0;
        inputIdentification.value = "";
        inputSocialReason.value = "";
        inputAddress.value = "";
        inputPhoneNumber.value = "";
        inputEmail.value = "";

        if (inputIdentification) {
            $bodyAcquirer.identification = "";
            inputIdentification.disabled = false;
        }
        if (inputSocialReason) {
            $bodyAcquirer.social_reason = ""
            inputSocialReason.disabled = false;
        }
        if (inputPhoneNumber) {
            $bodyAcquirer.phone_number = ""
            inputPhoneNumber.disabled = false;
        }
        if (inputAddress) {
            $bodyAcquirer.address = ""
            inputAddress.disabled = false;
        }
        if (inputEmail) {
            $bodyAcquirer.email = ""
            inputEmail.disabled = false;
        }

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
                window.localStorage.setItem("body_acquirer", JSON.stringify($bodyAcquirer));  
            }
        }
    }

    function loadData () {
        const dataJson = window.localStorage.getItem("body_acquirer");
        if (dataJson !== null) {
            const data: Acquirer = JSON.parse(dataJson);
            //console.log(data);
            selectedIdentificationTypeId.set(data.identification_type_id);
            if (data.identification_type_id !== 4) {
                setTimeout(() => {
                    $bodyAcquirer.identification = data.identification;
                    $bodyAcquirer.social_reason = data.social_reason;
                    $bodyAcquirer.address = data.address;
                    $bodyAcquirer.phone_number = data.phone_number;
                    $bodyAcquirer.email = data.email;
                }, 1000)
            }
        }
    }

    function removeData () {
        window.localStorage.removeItem("body_acquirer");
    }

    onMount(loadData);

</script>

<div class="flex flex-col border border-[--color-border] p-4 rounded-lg gap-4 w-fit place-self-center">
    <section class="bg-[--color-theme-1] rounded-md">
        <h3 class="font-medium text-center text-slate-50 p-1.5">Adquiriente</h3>
    </section>
    <section class="flex flex-col gap-4">
        <div class="flex flex-row gap-5">
            <label for="tipo_identificacion">Tipo de <br>identificación:</label>
            <div class="flex place-items-center">
                <Select optionSelected={selectedIdentificationTypeId} options={IdentificationTypes} alert={$alertsInputAcquirer.identification_type_id} pos={'down'}/>
            </div>
        </div>
        <div class="flex flex-row gap-5">
            <label for="identificacion">Identificación:</label>
            <input bind:this={inputIdentification} bind:value={$bodyAcquirer.identification} name="identificacion" class="w-[205px] outline-none border {$alertsInputAcquirer.identification ? 'border-red-500' : 'border-[--color-border]'} bg-transparent {inputIdentification ? (inputIdentification.disabled ? 'border-stone-300 text-transparent' : '') : ''} rounded-md px-1 ml-auto" type="text" on:input={()=>$alertsInputAcquirer.identification = false}>
        </div>
        <div class="flex flex-row gap-5">
            <label for="razon_social">Razón social:</label>
            <input bind:this={inputSocialReason} bind:value={$bodyAcquirer.social_reason} name="razon_social" class="w-[205px] outline-none border {$alertsInputAcquirer.social_reason ? 'border-red-500' : 'border-[--color-border]'} bg-transparent {inputSocialReason ? (inputSocialReason.disabled ? 'border-stone-300 text-transparent' : '') : ''} rounded-md px-1 ml-auto" type="text" on:input={()=>$alertsInputAcquirer.social_reason = false}>
        </div>
        <div class="flex flex-row gap-5">
            <label for="direccion">Dirección:</label>
            <input bind:this={inputAddress} bind:value={$bodyAcquirer.address} name="direccion" class="w-[205px] outline-none border {$alertsInputAcquirer.address ? 'border-red-500' : 'border-[--color-border]'} bg-transparent {inputAddress ? (inputAddress.disabled ? 'border-stone-300 text-transparent' : '') : ''} rounded-md px-1 ml-auto" type="text" on:input={()=>$alertsInputAcquirer.address = false}>
        </div>
        <div class="flex flex-row gap-5">
            <label for="telefono">Teléfono:</label>
            <input bind:this={inputPhoneNumber} bind:value={$bodyAcquirer.phone_number} name="telefono" class="w-[205px] outline-none border {$alertsInputAcquirer.phone_number ? 'border-red-500' : 'border-[--color-border]'} bg-transparent {inputPhoneNumber ? (inputPhoneNumber.disabled ? 'border-stone-300 text-transparent' : '') : ''} rounded-md px-1 ml-auto" type="text" on:input={(e)=>{$alertsInputAcquirer.phone_number = false}}>
        </div>
        <div class="flex flex-row gap-5">
            <label for="correo">Correo:</label>
            <input bind:this={inputEmail} bind:value={$bodyAcquirer.email} name="correo" class="w-[205px] outline-none border {$alertsInputAcquirer.email ? 'border-red-500' : 'border-[--color-border]'} bg-transparent {inputEmail ? (inputEmail.disabled ? 'border-stone-300 text-transparent' : '') : ''} rounded-md px-1 ml-auto" type="text" on:input={()=>$alertsInputAcquirer.email = false}>
        </div>
    </section>
</div>
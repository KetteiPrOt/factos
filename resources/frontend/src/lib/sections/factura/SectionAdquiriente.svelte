<script lang="ts">
    import Select from "$lib/components/Select.svelte";
    import { IdentificationTypes } from "$lib/interfaces/identification";
import type { Acquirer } from "$lib/interfaces/invoice";
    import type { HTMLInputAttributes } from "svelte/elements";
    import { writable, type Writable } from "svelte/store";

    

    export let bodyAcquirer: Writable<Acquirer>;

    let selectedIdentificationTypeId = writable(0);

    // Input Elements
    let inputIdentification: HTMLInputElement;
    let inputSocialReason: HTMLInputElement;
    let inputPhoneNumber: HTMLInputElement;
    let inputAddress: HTMLInputElement;
    let inputEmail: HTMLInputElement;


    $: {
        $bodyAcquirer.identification_type_id = $selectedIdentificationTypeId
    }

    $: {
        if ($selectedIdentificationTypeId === 4) {
            if (inputIdentification) {
                $bodyAcquirer.indentification = "";
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
                inputIdentification.disabled = false;
            }
            if (inputSocialReason) {
                inputSocialReason.disabled = false;
            }
            if (inputPhoneNumber) {
                inputPhoneNumber.disabled = false;
            }
            if (inputAddress) {
                inputAddress.disabled = false;
            }
            if (inputEmail) {
                inputEmail.disabled = false;
            }
            
        }
    }

    // $: {
    //     console.log($bodyAcquirer)
    // }

</script>

<div class="flex flex-col border border-[--color-border] p-4 rounded-lg gap-4 w-fit place-self-center">
    <section class="bg-[--color-theme-1] rounded-md">
        <h3 class="font-medium text-center text-slate-50 p-1.5">Adquiriente</h3>
    </section>
    <section class="flex flex-col gap-4">
        <div class="flex flex-row gap-5">
            <label for="tipo_identificacion">Tipo de <br>identificación:</label>
            <div class="flex place-items-center">
                <Select optionSelected={selectedIdentificationTypeId} options={IdentificationTypes} alert={false} pos={'down'}/>
            </div>
        </div>
        <div class="flex flex-row gap-5">
            <label for="identificacion">Identificación:</label>
            <input bind:this={inputIdentification} bind:value={$bodyAcquirer.indentification} name="identificacion" class="w-[205px] outline-none border border-[--color-border] bg-transparent {inputIdentification ? (inputIdentification.disabled ? 'border-stone-300' : '') : ''} rounded-md px-1 ml-auto" type="number">
        </div>
        <div class="flex flex-row gap-5">
            <label for="razon_social">Razón social:</label>
            <input bind:this={inputSocialReason} bind:value={$bodyAcquirer.social_reason} name="razon_social" class="w-[205px] outline-none border border-[--color-border] bg-transparent {inputSocialReason ? (inputSocialReason.disabled ? 'border-stone-300' : '') : ''} rounded-md px-1 ml-auto" type="text">
        </div>
        <div class="flex flex-row gap-5">
            <label for="direccion">Dirección:</label>
            <input bind:this={inputAddress} bind:value={$bodyAcquirer.address} name="direccion" class="w-[205px] outline-none border border-[--color-border] bg-transparent {inputAddress ? (inputAddress.disabled ? 'border-stone-300' : '') : ''} rounded-md px-1 ml-auto" type="text">
        </div>
        <div class="flex flex-row gap-5">
            <label for="telefono">Teléfono:</label>
            <input bind:this={inputPhoneNumber} bind:value={$bodyAcquirer.phone_number} name="telefono" class="w-[205px] outline-none border border-[--color-border] bg-transparent {inputPhoneNumber ? (inputPhoneNumber.disabled ? 'border-stone-300' : '') : ''} rounded-md px-1 ml-auto" type="number">
        </div>
        <div class="flex flex-row gap-5">
            <label for="correo">Correo:</label>
            <input bind:this={inputEmail} bind:value={$bodyAcquirer.email} name="correo" class="w-[205px] outline-none border border-[--color-border] bg-transparent {inputEmail ? (inputEmail.disabled ? 'border-stone-300' : '') : ''} rounded-md px-1 ml-auto" type="text">
        </div>
    </section>
</div>
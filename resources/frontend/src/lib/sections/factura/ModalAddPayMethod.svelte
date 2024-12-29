<script lang="ts">
    import Select from "$lib/components/Select.svelte";
import type { Ice } from "$lib/interfaces/ice";
    import type { PayMethod } from "$lib/interfaces/invoice";
    import { PayMethods, Times } from "$lib/interfaces/pay_methods";
    import type { ProductPost } from "$lib/interfaces/product";
    import type { Vat } from "$lib/interfaces/vat";
    import { onMount } from "svelte";
    import { writable, type Writable } from "svelte/store";
	import { blur, fade, slide } from "svelte/transition";
    
    // export let requestFunctions: {
    //     loadProducts: () => Promise<void>;
    // }
    export let visible: boolean;
    export let toggleModalAddPayMethod: ()=>void;

    export let bodyPaymentMethods: Writable<PayMethod[]>;

    const payMethodSelected = writable(0);
    const timeSelected = writable(0);

    let alertsInput = {
        payMethod: false,
        value: false,
        term: false,
        time: false
    }

    // New PayMethod
    let newPayMethod: PayMethod = {
        pay_method_id: 0,
        value: 0,
        term: 0,
        time: ''
    }

    function clearNewPayMethod () {
        newPayMethod = {pay_method_id: 0, value: 0, term: 0, time: ""};
        $payMethodSelected = 0;
        $timeSelected = 0;
    }

    //Update functions
    $: {
        if (typeof $payMethodSelected !== "undefined" || $payMethodSelected !== null) {
            newPayMethod.pay_method_id = $payMethodSelected;
            alertsInput.payMethod = false;
        }
    };

    $: {
        if (typeof $timeSelected !== "undefined" || $timeSelected !== null) {
            newPayMethod.time = Times.find(t => t.id === $timeSelected)?.name
            alertsInput.time = false;
        }
    };

    function updateValue () {
        if (typeof newPayMethod.value !== "undefined" || newPayMethod.value !== null) {
            alertsInput.value = false;
        }
    }

    function updateTerm () {
        if (typeof newPayMethod.term !== "undefined" || newPayMethod.term !== null) {
            alertsInput.term = false;
        }
    }

    $: {
        console.log(newPayMethod);
    }

    $: {
        console.log($bodyPaymentMethods)
    }

    // Validation function
    function validateNewPayMethod () {
        let valid = true;
        if (!newPayMethod.pay_method_id) {
            alertsInput.payMethod = true;
        };
        if (!newPayMethod.time) {
            alertsInput.time = true;
        };
        if (!newPayMethod.value) {
            alertsInput.value = true;
        };
        if (!newPayMethod.term) {
            alertsInput.term = true;
        };
        if (!Number.isInteger(newPayMethod.term)) {
            newPayMethod.term = parseInt(newPayMethod.term?.toFixed(0) as string);
        };

        Object.values(alertsInput).forEach(value => {
            if (value) {
                valid = false;
            };
        })

        return valid;
    }

    // Add function
    function addNewPayMethod () {
        if (validateNewPayMethod()) {
            $bodyPaymentMethods = [...$bodyPaymentMethods, newPayMethod];
            clearNewPayMethod();
            toggleModalAddPayMethod();
        }
    }

    // Additional functions
    function selectThisInput (e: MouseEvent) {
        const target = e.currentTarget as HTMLInputElement;
        target.select();
    }
    

</script>

{#if visible}
<button transition:blur class="fixed top-0 left-0 z-50 flex flex-col bg-blue-700/60 backdrop-blur w-full h-dvh" on:click={toggleModalAddPayMethod}>
    <button class="m-auto flex flex-col w-[409px] place-items-center gap-2" on:click={(e)=>{e.stopPropagation()}}>
        <button class=" flex flex-col gap-5 p-6 border border-[--color-border] bg-slate-100/80 rounded-md hover:cursor-default" >
            <section>
                <h3 class=" font-medium text-lg">
                    Detalle Método de pago
                </h3>
            </section>
            <section class="flex flex-col gap-3">
                <div class="flex flex-row gap-5">
                    <label for="code">Forma de pago:</label>
                    <div class="ml-auto">
                        <Select options={PayMethods} optionSelected={payMethodSelected} alert={alertsInput.payMethod} pos="down" />
                    </div>                    
                </div>
                <div class="flex flex-row gap-5">
                    <label for="value">Valor:</label>
                    <input bind:value={newPayMethod.value} name="value" class="border border-[--color-border] text-center {alertsInput.value ? 'border-red-500' : ''} bg-transparent outline-none rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="number" on:input={updateValue} on:click={(e)=>selectThisInput(e)}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="term">Plazo:</label>
                    <input bind:value={newPayMethod.term} name="term" class="border border-[--color-border] text-center {alertsInput.term ? 'border-red-500' : ''} bg-transparent outline-none rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="number" on:input={updateTerm} on:click={(e)=>selectThisInput(e)}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="time">Tiempo:</label>
                    <div class="ml-auto">
                        <Select options={Times} optionSelected={timeSelected} alert={alertsInput.time} />
                    </div>
                </div>
                
            </section>
            <section class="flex flex-row gap-5 self-start text-slate-50">
                <button class="h-[34px] bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={addNewPayMethod}>
                    Guardar
                </button>
                <button class="h-[34px] box-border border border-[--color-theme-1] text-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200" on:click={toggleModalAddPayMethod}>
                    Salir
                </button>
            </section>
        </button>
        
    </button>
    
</button>
{/if}

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
    export let toggleModalEditPayMethod: ()=>void;

    export let bodyPaymentMethods: Writable<PayMethod[]>;

    // Current PayMethod
    export let indexCurrentPayMethod: Writable<number>;
    let currentPayMethod:PayMethod = {
        pay_method_id: 0,
        value: 0,
        term: 0,
        time: ''
    };
    
    const payMethodSelected = writable(0);
    const timeSelected = writable(0);

    let alertsInput = {
        payMethod: false,
        value: false,
        term: false,
        time: false
    }


    function clearCurrentPayMethod () {
        currentPayMethod = {pay_method_id: 0, value: 0, term: 0, time: ""};
        $payMethodSelected = 0;
        $timeSelected = 0;
    }

    function updateCurrentPayMethod (id: number, value: number, term: number, time: number) {
        $payMethodSelected = id;
        currentPayMethod.value = value;
        currentPayMethod.term = term;
        $timeSelected = time
    }

    $: {
        $indexCurrentPayMethod;
        if (typeof $indexCurrentPayMethod === "number") {
            let pm = $bodyPaymentMethods[$indexCurrentPayMethod];
            if (pm) {
                updateCurrentPayMethod(pm.pay_method_id, pm.value, pm.term as number, Times.find(t=>t.name === pm.time)?.id as number)
                console.log("update currentPayMethod")
            }
        }
    }

    //Update functions
    $: {
        if (typeof $payMethodSelected !== "undefined" || $payMethodSelected !== null) {
            currentPayMethod.pay_method_id = $payMethodSelected;
            alertsInput.payMethod = false;
        }
    };

    $: {
        if (typeof $timeSelected !== "undefined" || $timeSelected !== null) {
            currentPayMethod.time = Times.find(t => t.id === $timeSelected)?.name
            alertsInput.time = false;
        }
    };

    function updateValue () {
        if (typeof currentPayMethod.value !== "undefined" || currentPayMethod.value !== null) {
            alertsInput.value = false;
        }
    }

    function updateTerm () {
        if (typeof currentPayMethod.term !== "undefined" || currentPayMethod.term !== null) {
            alertsInput.term = false;
        }
    }

    $: {
        console.log(currentPayMethod);
    }

    $: {
        console.log($bodyPaymentMethods)
    }

    // Validation function
    function validateCurrentPayMethod () {
        let valid = true;
        if (!currentPayMethod.pay_method_id) {
            alertsInput.payMethod = true;
        };
        if (!currentPayMethod.time) {
            alertsInput.time = true;
        };
        if (!currentPayMethod.value) {
            alertsInput.value = true;
        };
        if (!currentPayMethod.term) {
            alertsInput.term = true;
        };
        if (!Number.isInteger(currentPayMethod.term)) {
            currentPayMethod.term = parseInt(currentPayMethod.term?.toFixed(0) as string);
        };

        Object.values(alertsInput).forEach(value => {
            if (value) {
                valid = false;
            };
        })

        return valid;
    }

    // Add function
    function editCurrentPayMethod () {
        if (validateCurrentPayMethod()) {
            $bodyPaymentMethods[$indexCurrentPayMethod] = currentPayMethod;
            clearCurrentPayMethod();
            toggleModalEditPayMethod();
        }
    }
    

</script>

{#if visible}
<button transition:blur class="fixed top-0 left-0 z-50 flex flex-col bg-blue-700/60 backdrop-blur w-full h-dvh" on:click={toggleModalEditPayMethod}>
    <button class="m-auto flex flex-col w-[409px] place-items-center gap-2" on:click={(e)=>{e.stopPropagation()}}>
        <button class=" flex flex-col gap-5 p-6 border border-[--color-border] bg-slate-100/80 rounded-md hover:cursor-default" >
            <section>
                <h3 class=" font-medium text-lg">
                    Modificando - Detalle Método de pago
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
                    <input bind:value={currentPayMethod.value} name="value" class="border border-[--color-border] text-center {alertsInput.value ? 'border-red-500' : ''} bg-transparent outline-none rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="number" on:input={updateValue}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="term">Plazo:</label>
                    <input bind:value={currentPayMethod.term} name="term" class="border border-[--color-border] text-center {alertsInput.term ? 'border-red-500' : ''} bg-transparent outline-none rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="number" on:input={updateTerm}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="time">Tiempo:</label>
                    <div class="ml-auto">
                        <Select options={Times} optionSelected={timeSelected} alert={alertsInput.time} />
                    </div>
                </div>
                
            </section>
            <section class="flex flex-row gap-5 self-start text-slate-50">
                <button class="h-[34px] bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={editCurrentPayMethod}>
                    Guardar
                </button>
                <button class="h-[34px] box-border border border-[--color-theme-1] text-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200" on:click={toggleModalEditPayMethod}>
                    Salir
                </button>
            </section>
        </button>
        
    </button>
    
</button>
{/if}

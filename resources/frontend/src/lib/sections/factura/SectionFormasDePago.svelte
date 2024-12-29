<script lang="ts">
    import type { PayMethod } from "$lib/interfaces/invoice";
    import { PayMethods } from "$lib/interfaces/pay_methods";
    import { onMount } from "svelte";
    import { Icon } from "svelte-icons-pack";
    import { BiPlus, BiTrash } from "svelte-icons-pack/bi";
    import { writable, type Writable } from "svelte/store";
    import { fade } from "svelte/transition";

    export let toggleModalAddPayMethod: () => void;
    export let toggleModalEditPayMethod: () => void;
    
    export let bodyPaymentMethods: Writable<PayMethod[]>;

    export let indexCurrentPayMethod: Writable<number>;
    
    
    function findNameOfPayMethod (id: number) {
        let name = ""
        let res = PayMethods.find((pm) => {
            if (pm.id === id) {
                return true;
            }
        })?.name;

        if (res) { name = res };
        return name;
    }

    // Functions Pay Methods

    function deletePayMethod (index: number) {
        if (typeof index !== "number") {return};

        $bodyPaymentMethods = [...$bodyPaymentMethods.filter(pm => {
            if ($bodyPaymentMethods.indexOf(pm) !== index) {
                return true;
            }
        })];

    }

    function selectPayMethod (index: number) {
        console.log(index);
        if (typeof index === "number") {
            $indexCurrentPayMethod = index;
            toggleModalEditPayMethod();
        }
    }


    
</script>

<div class="flex flex-col border border-[--color-border] p-4 rounded-lg gap-4 box-border w-fit max-w-full place-self-center">
    <section class="bg-[--color-theme-1] rounded-md">
        <h3 class="font-medium text-center text-slate-50 p-1.5">Formas de pago</h3>
    </section>
    <section class="max-w-full">
        <div class="block w-full  rounded-md max-w-full overflow-x-auto">
            <table class="max-w-full w-full border-collapse text-center rounded-md">
                <thead class="bg-[--color-theme-1]">
                    <tr>
                        <th>Forma de pago</th>
                        <th>Valor</th>
                        <th>Plazo</th>
                        <th>Tiempo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- <tr>
                        <td>Forma de pago</td>
                        <td>Valor</td>
                        <td>Plazo</td>
                        <td>Tiempo</td>
                        <td>Acciones</td>
                    </tr> -->
                    {#each $bodyPaymentMethods as payMethod}
                    <tr in:fade={{delay: ($bodyPaymentMethods.indexOf(payMethod)*50)}} class="hover:bg-slate-300" data-id="{payMethod.pay_method_id}">
                        <td>
                            <button class="w-full h-full p-2" on:click={(e)=>selectPayMethod($bodyPaymentMethods.indexOf(payMethod))}>
                                {findNameOfPayMethod(payMethod.pay_method_id)}
                            </button>
                        </td>
                        <td>
                            <button class="w-full h-full p-2" on:click={(e)=>selectPayMethod($bodyPaymentMethods.indexOf(payMethod))}>
                                {payMethod.value}
                            </button>
                        </td>
                        <td>
                            <button class="w-full h-full p-2" on:click={(e)=>selectPayMethod($bodyPaymentMethods.indexOf(payMethod))}>
                                {payMethod.term}
                            </button>
                        </td>
                        <td>
                            <button class="w-full h-full p-2" on:click={(e)=>selectPayMethod($bodyPaymentMethods.indexOf(payMethod))}>
                                {payMethod.time}
                            </button>
                        </td>
                        <td>
                            <button class="flex w-full h-full place-content-center place-items-center p-2 text-[--color-theme-1] hover:text-blue-500" on:click={() => deletePayMethod($bodyPaymentMethods.indexOf(payMethod))}>
                                <Icon src={BiTrash} size={22} />
                            </button>
                        </td>
                    </tr>
                    {/each}
                    
                </tbody>
            </table>
        </div>
    </section>
    <section class="flex flex-row gap-3 text-slate-50">
        <!-- <button class="bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600">
            Efectivo
        </button>
        <button class="bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600">
            Tarjeta de débito
        </button>
        <button class="bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600">
            Tarjeta de crédito
        </button> -->
        <button class="bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={toggleModalAddPayMethod}>
            <div class="flex flex-row gap-1 place-items-center place-content-center">
                <Icon src={BiPlus} />
                <span>
                    Agregar 
                </span>
            </div>
        </button>
    </section>
</div>

<style>
    th {
        border: .5px solid var(--color-border);
        color: #f8fafc;
        padding: .5rem;
    }
    td {
        border: .5px solid var(--color-border);
        padding: .5rem;
    }
</style>
<script lang="ts">
    import type { AdditionalField } from "$lib/interfaces/invoice";
    import { type Writable } from "svelte/store";
	import { blur } from "svelte/transition";
    
    export let visible: boolean;
    export let toggleModalAddAdditionalField: ()=>void;

    export let bodyAdditionalFields: Writable<AdditionalField[] | undefined>;

    let alertsInput = {
        name: false,
        description: false
    }

    // New AdditionalField
    let newAdditionalField: AdditionalField = {name: "", description: ""};

    function clearNewAdditionalField () {
        newAdditionalField = {name: "", description: ""};
    }

    

    function updateName () {
        if (typeof newAdditionalField.name !== "undefined" || newAdditionalField.name !== null) {
            alertsInput.name = false;
        }
    }

    function updateDescription () {
        if (typeof newAdditionalField.description !== "undefined" || newAdditionalField.description !== null) {
            alertsInput.description = false;
        }
    }

    // Validation function
    function validateNewAdditionalField () {
        let valid = true;
        if (!newAdditionalField.name) {
            alertsInput.name = true;
        };
        if (!newAdditionalField.description) {
            alertsInput.description = true;
        };

        Object.values(alertsInput).forEach(value => {
            if (value) {
                valid = false;
            };
        })

        return valid;
    }

    // Add function
    function addNewAdditionalField () {
        if (validateNewAdditionalField() && Array.isArray($bodyAdditionalFields)) {
            $bodyAdditionalFields = [...$bodyAdditionalFields, newAdditionalField];
            clearNewAdditionalField();
            toggleModalAddAdditionalField();
        }
    }

    // Additional functions
    function selectThisInput (e: MouseEvent) {
        const target = e.currentTarget as HTMLInputElement;
        target.select();
    }    

</script>

{#if visible}
<button transition:blur class="fixed top-0 left-0 z-50 flex flex-col bg-blue-700/60 backdrop-blur w-full h-dvh" on:click={toggleModalAddAdditionalField}>
    <button class="m-auto flex flex-col w-[409px] place-items-center gap-2" on:click={(e)=>{e.stopPropagation()}}>
        <button class=" flex flex-col gap-5 p-6 border border-[--color-border] bg-slate-100/80 rounded-md hover:cursor-default" >
            <section>
                <h3 class=" font-medium text-lg">
                    Detalle Campo Adicional
                </h3>
            </section>
            <section class="flex flex-col gap-3">
                <div class="flex flex-row gap-5">
                    <label for="name" >Nombre:</label>
                    <input bind:value={newAdditionalField.name} class="border border-[--color-border] text-center {alertsInput.name ? 'border-red-500' : ''} bg-transparent outline-none rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={updateName} on:click={(e)=>selectThisInput(e)}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="description">Descripción:</label>
                    <input bind:value={newAdditionalField.description} name="desciption" class="border border-[--color-border] text-center {alertsInput.description ? 'border-red-500' : ''} bg-transparent outline-none rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={updateDescription} on:click={(e)=>selectThisInput(e)}>
                </div>
                
            </section>
            <section class="flex flex-row gap-5 self-start text-slate-50">
                <button class="h-[34px] bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={addNewAdditionalField}>
                    Guardar
                </button>
                <button class="h-[34px] box-border border border-[--color-theme-1] text-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200" on:click={toggleModalAddAdditionalField}>
                    Salir
                </button>
            </section>
        </button>
        
    </button>
    
</button>
{/if}

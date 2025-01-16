<script lang="ts">
    import type { AdditionalField, PayMethod } from "$lib/interfaces/invoice";
    import { type Writable } from "svelte/store";
	import { blur } from "svelte/transition";
    
    export let visible: boolean;
    export let toggleModalEditAdditionalField: ()=>void;

    export let bodyAdditionalFields: Writable<AdditionalField[] | undefined>;

    let alertsInput = {
        name: false,
        description: false
    }

    // Current AdditionalField
    export let indexCurrentAdditionalField: Writable<number>;
    let currentAdditionalField: AdditionalField = {name: "", description: ""};

    function clearCurrentAdditionalField () {
        currentAdditionalField = {name: "", description: ""};
    }

    

    function updateName () {
        if (typeof currentAdditionalField.name !== "undefined" || currentAdditionalField.name !== null) {
            alertsInput.name = false;
        }
    }

    function updateDescription () {
        if (typeof currentAdditionalField.description !== "undefined" || currentAdditionalField.description !== null) {
            alertsInput.description = false;
        }
    }

    function updateCurrentAdditionalField (name: string, description: string) {
        currentAdditionalField.name = name;
        currentAdditionalField.description = description;
    }

    $: {
        $indexCurrentAdditionalField;
        if (typeof $indexCurrentAdditionalField === "number" && Array.isArray($bodyAdditionalFields)) {
            let af = $bodyAdditionalFields[$indexCurrentAdditionalField];
            if (af) {
                updateCurrentAdditionalField(af.name, af.description);
            }
        }
    }

    // Validation function
    function validateCurrentAdditionalField () {
        let valid = true;
        if (!currentAdditionalField.name) {
            alertsInput.name = true;
        };
        if (!currentAdditionalField.description) {
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
    function editCurrentAdditionalField () {
        if (validateCurrentAdditionalField() && Array.isArray($bodyAdditionalFields)) {
            $bodyAdditionalFields[$indexCurrentAdditionalField] = currentAdditionalField;
            clearCurrentAdditionalField();
            toggleModalEditAdditionalField();
        }
    }

    // Additional functions
    function selectThisInput (e: MouseEvent) {
        const target = e.currentTarget as HTMLInputElement;
        target.select();
    }    

</script>

{#if visible}
<button transition:blur class="fixed top-0 left-0 z-50 flex flex-col bg-blue-700/60 backdrop-blur w-full h-dvh" on:click={toggleModalEditAdditionalField}>
    <button class="m-auto flex flex-col w-[409px] place-items-center gap-2" on:click={(e)=>{e.stopPropagation()}}>
        <button class=" flex flex-col gap-5 p-6 border border-[--color-border] bg-slate-100/80 rounded-md hover:cursor-default" >
            <section>
                <h3 class=" font-medium text-lg">
                    Modificando - Detalle Campo Adicional
                </h3>
            </section>
            <section class="flex flex-col gap-3">
                <div class="flex flex-row gap-5">
                    <label for="name" >Nombre:</label>
                    <input bind:value={currentAdditionalField.name} class="border border-[--color-border] text-center {alertsInput.name ? 'border-red-500' : ''} bg-transparent outline-none rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={updateName} on:click={(e)=>selectThisInput(e)}>
                </div>
                <div class="flex flex-row gap-5">
                    <label for="description">Descripción:</label>
                    <input bind:value={currentAdditionalField.description} name="desciption" class="border border-[--color-border] text-center {alertsInput.description ? 'border-red-500' : ''} bg-transparent outline-none rounded-md px-1 ml-auto h-[26px] w-[205px] place-self-center" type="text" on:input={updateDescription} on:click={(e)=>selectThisInput(e)}>
                </div>
                
            </section>
            <section class="flex flex-row gap-5 self-start text-slate-50">
                <button class="h-[34px] bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={editCurrentAdditionalField}>
                    Guardar
                </button>
                <button class="h-[34px] box-border border border-[--color-theme-1] text-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200" on:click={toggleModalEditAdditionalField}>
                    Salir
                </button>
            </section>
        </button>
        
    </button>
    
</button>
{/if}

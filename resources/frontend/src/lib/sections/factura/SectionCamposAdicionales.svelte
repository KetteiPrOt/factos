<script lang="ts">
    import type { AdditionalField } from "$lib/interfaces/invoice";
    import { onMount } from "svelte";
    import { Icon } from "svelte-icons-pack";
    import { BiPlus, BiTrash } from "svelte-icons-pack/bi";
    import type { Writable } from "svelte/store";
    import { fade } from "svelte/transition";

    export let saving: boolean;
    export let success: boolean;

    export let bodyAdditionalFields: Writable<AdditionalField[] | undefined>;
    export let indexCurrentAdditionalField: Writable<number>;
    export let toggleModalAddAdditionalField: () => void;
    export let toggleModalEditAdditionalField: () => void;


    // Functions Additional Fields

    function deleteAdditionalField (index: number) {
        if (typeof index !== "number") {return};

        if (typeof $bodyAdditionalFields !== "undefined" && Array.isArray($bodyAdditionalFields)) {
            $bodyAdditionalFields = [...$bodyAdditionalFields.filter(af => {
                if (typeof $bodyAdditionalFields !== "undefined") {
                    if ($bodyAdditionalFields?.indexOf(af) !== index) {
                        return true;

                    }
                };
            })];
        }
    };

    function selectAdditionalField (index: number) {
        console.log(index);
        if (typeof index === "number") {
            $indexCurrentAdditionalField = index;
            toggleModalEditAdditionalField();
        }
    };

    $: {
        if (success) {
            if (typeof window !== "undefined") {
                removeData();
            }
        }
    }

    $: {
        if (saving) {
            if (typeof window !== "undefined") {
                window.localStorage.setItem("list_additional_fields", JSON.stringify($bodyAdditionalFields));
            }
        }
    }

    function loadData () {
        const dataJson = window.localStorage.getItem("list_additional_fields");
        if (dataJson !== null) {
            const data: AdditionalField[] = JSON.parse(dataJson);
            bodyAdditionalFields.set(data);
        }
    }

    function removeData () {
        window.localStorage.removeItem("list_additional_fields");
    }

    onMount(loadData);

</script>

<div class="flex flex-col border border-[--color-border] p-4 rounded-lg gap-4 box-border w-full max-w-full place-self-center">
    <section class="bg-[--color-theme-1] rounded-md">
        <h3 class="font-medium text-center text-slate-50 p-1.5">Campos adicionales</h3>
    </section>
    <section class="max-w-full">
        <div class="block w-full  rounded-md max-w-full overflow-x-auto">
            <table class="max-w-full w-full border-collapse text-center rounded-md">
                <thead class="bg-[--color-theme-1]">
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {#if Array.isArray($bodyAdditionalFields)}
                    {#each $bodyAdditionalFields as additionalField}
                    <tr in:fade={{delay: ($bodyAdditionalFields.indexOf(additionalField)*50)}} class="hover:bg-slate-300" >
                        <td>
                            <button class="w-full h-full p-2" on:click={(e)=>selectAdditionalField($bodyAdditionalFields ? $bodyAdditionalFields.indexOf(additionalField) : 0)}>
                                {additionalField.name}
                            </button>
                        </td>
                        <td>
                            <button class="w-full h-full p-2" on:click={(e)=>selectAdditionalField($bodyAdditionalFields ? $bodyAdditionalFields.indexOf(additionalField) : 0)}>
                                {additionalField.description}
                            </button>
                        </td>
                        <td>
                            <button class="flex w-full h-full place-content-center place-items-center p-2 text-[--color-theme-1] hover:text-blue-500" on:click={() => deleteAdditionalField($bodyAdditionalFields ? $bodyAdditionalFields.indexOf(additionalField) : 0)}>
                                <Icon src={BiTrash} size={22} />
                            </button>
                        </td>
                    </tr>
                    {/each}                        
                    {/if}
                    
                </tbody>
            </table>
        </div>
    </section>
    <section class="flex flex-row gap-3 text-slate-50">
        <button class="bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={toggleModalAddAdditionalField}>
            <div class="flex flex-row gap-1 place-items-center place-content-center">
                <Icon src={BiPlus} />
                <span>
                    Agregar campo adicional
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
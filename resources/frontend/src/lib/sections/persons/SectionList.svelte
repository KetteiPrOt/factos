<script lang="ts">
    import type { PaginationPersons } from "$lib/interfaces/pagination";
    import type { Person, PersonGet } from "$lib/interfaces/person"
	import { onMount } from "svelte";

	import { Icon } from "svelte-icons-pack";
	import { AiFillFastBackward, AiFillFastForward } from "svelte-icons-pack/ai";
	import { BiDownload } from "svelte-icons-pack/bi";
	import type { Writable } from "svelte/store";
    import { fade } from "svelte/transition";

    export let requestFunctions: {
        loadPersons: (url?: string | undefined) => Promise<void>;
        getPersonById: () => Promise<void>;
    }

    export let persons: Writable<Person[]>;
    export let personSelected: Writable<PersonGet>;

    export let toogleModalViewPersonVisible: ()=>void;

    export let idToSearch: Writable<string>;

    export let paginationData: Writable<PaginationPersons>;

    

    function selectPerson (e: MouseEvent) {
        const target = e.target as HTMLButtonElement;
        const td = target.parentElement as HTMLTableCellElement
        const tr = td.parentElement as HTMLTableRowElement
        
        if (tr.dataset.id) {
            $idToSearch = tr.dataset.id;
            $personSelected = {id: 0, identification: "", social_reason: "", email: "", created_at: "", updated_at: "", identification_type_id: 0, user_id: 0};
            requestFunctions.getPersonById();
            toogleModalViewPersonVisible();
        }
    }
   

    function loadNextPage () {
        if ($paginationData.links?.next) {
            requestFunctions.loadPersons($paginationData.links?.next)
        }
    }

    function loadPrevPage () {
        if ($paginationData.links?.prev) {
            requestFunctions.loadPersons($paginationData.links?.prev)
        }
    }
    
    onMount(()=>requestFunctions.loadPersons(undefined))

</script>
<section class="max-w-full">
    <h3 class="font-semibold text-lg px-3 py-1">
        Lista de clientes
    </h3>
    <div class="block w-full  rounded-md max-w-full overflow-x-auto">
        <table class="max-w-full w-full border-collapse text-center">
            <thead class="bg-[--color-theme-1]">
                <tr>
                    <th>Identificación</th>
                    <th>Razón social</th>
                    <th>Correo</th>
                    <th>Tipo de identificación</th>
                </tr>
            </thead>
            <tbody>
                {#each $persons as person}
                <tr in:fade={{delay: ($persons.indexOf(person)*50)}} class="hover:bg-slate-300" data-id="{person.id}">
                    <td>
                        <button class="w-full h-full p-2" on:click={(e)=>selectPerson(e)}>
                            {person.identification}
                        </button>
                    </td>
                    <td>
                        <button class="w-full h-full p-2" on:click={(e)=>selectPerson(e)}>
                            {person.social_reason}
                        </button>
                    </td>
                    <td>
                        <button class="w-full h-full p-2" on:click={(e)=>selectPerson(e)}>
                            {person.email}
                        </button>
                    </td>
                    <td>
                        <button class="w-full h-full p-2" on:click={(e)=>selectPerson(e)}>
                            {person.identification_type}
                        </button>
                    </td>
                </tr>
                {/each}
            </tbody>
        </table>
    </div>
    <div class="w-full bg-[--color-theme-1] flex flex-row justify-center text-slate-50 p-1 place-items-center gap-4 rounded-b-md">
        <button class="flex flex-row gap-2 place-items-center bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600 text-slate-50" on:click={loadPrevPage}>
            <Icon src={AiFillFastBackward} size={20}/>
        </button>
        <div class="flex flex-row gap-1">
            <span>
                {#if $paginationData}
                    {#if $paginationData.meta.total > 0}
                        {$paginationData.meta.current_page}
                    {:else}
                        0
                    {/if}
                {:else}
                    0
                {/if}
            </span>
            <span>
                de
            </span>
            <span>
                {#if $paginationData}
                    {#if $paginationData.meta.total > 0}
                        {$paginationData.meta.last_page}
                    {:else}
                        0
                    {/if}
                {:else}
                    0
                {/if}
            </span>

        </div>
        <button class="flex flex-row gap-2 align-middle bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600 text-slate-50" on:click={loadNextPage}>
            <Icon src={AiFillFastForward} size={20}/>
        </button>
    </div>
    <button class="flex flex-row gap-2 text-[--color-theme-1] mt-2 px-3 py-1 rounded-md border border-transparent hover:border-[--color-theme-1] hover:bg-slate-200">
        <Icon src={BiDownload} size={20}/>
        <span>Descargar reporte</span>
    </button>
</section>

<style>
    th {
        border: .5px solid var(--color-border);
        color: #f8fafc;
        padding: .5rem;
    }
    td {
        border: .5px solid var(--color-border);
        /* padding: .5rem; */
    }
    tbody tr:last-child td:first-child {
        border-radius: 0%;
    } 
</style>
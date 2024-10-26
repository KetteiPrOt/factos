<script lang="ts">
	import { Icon } from "svelte-icons-pack";
	import { AiOutlineSearch } from "svelte-icons-pack/ai";
	import type { Writable } from "svelte/store";
    
    export let socialReasonToSearch: Writable<string>;
    export let identificationToSearch: Writable<string>;

    export let requestFunctions: {
    loadPersons: (url?: string | undefined) => Promise<void>;
    getPersonById: () => Promise<void>;
}

    function updateSocialReasonToSearch (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        $socialReasonToSearch = value;
    }

    function updateIdentificationToSearch (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        $identificationToSearch = value;
    }

    function detectEnter (e: KeyboardEvent) {
        if (e.key === "Enter") {
            searchPersons();
        }
    }

    async function searchPersons () {
        requestFunctions.loadPersons(`/api/persons?social_reason=${$socialReasonToSearch}&identification=${$identificationToSearch}`)
        
    }

</script>
<div class="flex flex-col gap-4 justify-center">
    <section class="flex flex-wrap gap-4 justify-center">
        <div class="flex flex-row gap-2">
            <label for="social_reason">
                Razón social:
            </label>
            <input name="social_reason" class="border border-[--color-border] bg-transparent rounded-md px-1 ml-auto" type="text" value={$socialReasonToSearch} on:input={(e)=>updateSocialReasonToSearch(e)} on:keypress={(e)=>detectEnter(e)}>
        </div>
        <div class="flex flex-row gap-2">
            <label for="identification">
                Identificación:
            </label>
            <input name="identification" class="border border-[--color-border] bg-transparent rounded-md px-1 ml-auto" type="text" value={$identificationToSearch} on:input={(e)=>updateIdentificationToSearch(e)} on:keypress={(e)=>detectEnter(e)}>
        </div>
    </section>
    <section class="flex justify-center">
        <button class="flex flex-row gap-2 place-items-center bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600 text-slate-50" on:click={searchPersons}>
            <Icon src={AiOutlineSearch} />
            <span>Buscar</span>
        </button>
    </section>

</div>
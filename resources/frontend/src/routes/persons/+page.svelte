<script lang="ts">
    import type { Pagination, PaginationPersons } from '$lib/interfaces/pagination';
    import type { Person, PersonGet } from '$lib/interfaces/person';
    import type { Product, ProductGet } from '$lib/interfaces/product';
    import ModalNewPerson from '$lib/sections/persons/ModalNewPerson.svelte';
    import ModalViewPerson from '$lib/sections/persons/ModalViewPerson.svelte';
	import SectionList from '$lib/sections/persons/SectionList.svelte';
	import SectionOptions from '$lib/sections/persons/SectionOptions.svelte';
	import SectionSearch from '$lib/sections/persons/SectionSearch.svelte';
    import ModalNewProduct from '$lib/sections/products/ModalNewProduct.svelte';
    import type { Writable } from 'svelte/store';
	import { writable } from 'svelte/store';
	import { fade } from 'svelte/transition';

    let modalNewPersonVisible = false;
    let modalViewPersonVisible = false;

    // Search Params
	export const socialReasonToSearch = writable("");
	export const identificationToSearch = writable("");

	export const persons: Writable<Person[]> = writable([]);

    export const paginationData: Writable<PaginationPersons> = writable();

	export const idToSearch = writable("");
	export const personSelected: Writable<PersonGet> = writable({id: 0, identification: "", social_reason: "", email: "", created_at: "", updated_at: "", identification_type_id: 0, user_id: 0});

    function toogleModalNewPersonVisible () {
        modalNewPersonVisible = !modalNewPersonVisible;
    }

    function toogleModalViewPersonVisible () {
        modalViewPersonVisible = !modalViewPersonVisible;
    }

	//REQUEST FUNCTIONS
	async function loadPersons (url: string | undefined = '/api/persons?page=1') {
        const res = await fetch(url,
            {
                headers: {'Accept': 'application/json'}
            }
        );
        
        $persons = []
        const data: PaginationPersons = await res.json();
        paginationData.set(data)
        if (Array.isArray(data.data)) {
            $persons = data.data
        }
        //console.log(data.data)

        if (res.status === 401) {
            window.location.href = 'login';
        }
    };

	async function getPersonById () {
        if (!$idToSearch) { return }

        const res = await fetch('/api/persons/'+$idToSearch, {
            credentials: "include",
            headers: {
                'Accept': 'application/json'
            }
        })

        if (res.status === 200) {
            const person: PersonGet = await res.json()
    
            $personSelected = person;
            $personSelected.new = true;
        }

    }

	const requestFunctions = {
		loadPersons: loadPersons,
		getPersonById: getPersonById
	}

</script>

<div in:fade class="flex flex-col p-5 gap-5 w-fit h-full max-w-full">
	<h2 class="font-bold text-3xl text-center">Mis clientes</h2>
	<div class="flex flex-col gap-7">
		<SectionSearch socialReasonToSearch={socialReasonToSearch} identificationToSearch={identificationToSearch} requestFunctions={requestFunctions} />
		<SectionOptions toogleModalNewPersonVisible={toogleModalNewPersonVisible} requestFunctions={requestFunctions} />
		<SectionList persons={persons} requestFunctions={requestFunctions} toogleModalViewPersonVisible={toogleModalViewPersonVisible} personSelected={personSelected} idToSearch={idToSearch} paginationData={paginationData} />
	</div>
	<ModalNewPerson requestFunctions={requestFunctions} visible={modalNewPersonVisible} toogleModalNewPersonVisible={toogleModalNewPersonVisible} />
	<ModalViewPerson requestFunctions={requestFunctions} visible={modalViewPersonVisible} toogleModalViewPersonVisible={toogleModalViewPersonVisible} personSelected={personSelected} idToSearch={idToSearch} />
</div>

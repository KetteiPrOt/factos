<script lang="ts">
    import type { Establishments, EstablishmentsGet } from '$lib/interfaces/establishments';
    import type { Pagination } from '$lib/interfaces/pagination';
    import type { Product, ProductGet } from '$lib/interfaces/product';
	import ModalNewProduct from '$lib/sections/establishments/ModalNewEstab.svelte';
    import ModalViewProduct from '$lib/sections/establishments/ModalViewEstab.svelte';
	import SectionList from '$lib/sections/establishments/SectionList.svelte';
	import SectionOptions from '$lib/sections/establishments/SectionOptions.svelte';
	import SectionSearch from '$lib/sections/establishments/SectionSearch.svelte';
    import type { Writable } from 'svelte/store';
	import { writable } from 'svelte/store';
	import { fade } from 'svelte/transition';

    let modalNewEstabVisible = false;
    let modalViewEstabVisible = false;

	export const nameToSearch = writable("");
	export const codeToSearch = writable("");
	export const establishments: Writable<Establishments[]> = writable([]);

    // export const paginationData: Writable<Pagination> = writable();

	export const idToSearch = writable("");
	export const estabSelected: Writable<EstablishmentsGet> = writable({ code: 0, address: "", commercial_name: "" });

    function toogleModalNewEstabVisible () {
        modalNewEstabVisible = !modalNewEstabVisible;
    }

    function toogleModalViewEstabVisible () {
        modalViewEstabVisible = !modalViewEstabVisible;
    }

	//REQUEST FUNCTIONS
	async function loadEstablishments (url: string | undefined = '/api/establishments') {
        const res = await fetch(url,
            {
                headers: {'Accept': 'application/json'}
            }
        );
        
        $establishments = []
        // const data: Pagination = await res.json();
        // paginationData.set(data)
        const data: Establishments[] = await res.json();
        if (Array.isArray(data)) {
            $establishments = data;
        }
        // console.log(data)

        if (res.status === 401) {
            window.location.href = 'login';
        }
    };

	async function getEstablishmentsById () {
        if (!$idToSearch) { return }

        const res = await fetch('/api/establishments/'+$idToSearch, {
            credentials: "include",
            headers: {
                'Accept': 'application/json'
            }
        })

        if (res.status === 200) {
            const estab: EstablishmentsGet = await res.json()
    
            $estabSelected = estab;
            $estabSelected.new = true;
        }

    }

	const requestFunctions = {
		loadEstablishments: loadEstablishments,
		getEstablishmentsById: getEstablishmentsById
	}

</script>

<div in:fade class="flex flex-col p-5 gap-5 w-fit h-full max-w-full">
	<h2 class="font-bold text-3xl text-center">Mis Establecimientos</h2>
	<div class="flex flex-col gap-7">
		<SectionSearch nameToSearch={nameToSearch} codeToSearch={codeToSearch} requestFunctions={requestFunctions} />
		<SectionOptions toogleModalNewEstabVisible={toogleModalNewEstabVisible} requestFunctions={requestFunctions} />
		<SectionList establishments={establishments} requestFunctions={requestFunctions} toogleModalViewEstabVisible={toogleModalViewEstabVisible} estabSelected={estabSelected} idToSearch={idToSearch} />
	</div>
	<ModalNewProduct requestFunctions={requestFunctions} visible={modalNewEstabVisible} toogleModalNewEstabVisible={toogleModalNewEstabVisible} />
	<ModalViewProduct requestFunctions={requestFunctions} visible={modalViewEstabVisible} toogleModalViewEstabVisible={toogleModalViewEstabVisible} estabSelected={estabSelected} idToSearch={idToSearch} />
</div>

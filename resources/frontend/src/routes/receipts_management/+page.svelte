<script lang="ts">
    import type { Pagination } from '$lib/interfaces/pagination';
    import type { Product, ProductGet } from '$lib/interfaces/product';
	import ModalNewProduct from '$lib/sections/receipts_management/ModalNewProduct.svelte';
    import ModalViewProduct from '$lib/sections/receipts_management/ModalViewProduct.svelte';
	import SectionList from '$lib/sections/receipts_management/SectionList.svelte';
	import SectionOptions from '$lib/sections/receipts_management/SectionOptions.svelte';
	import SectionSearch from '$lib/sections/receipts_management/SectionSearch.svelte';
    import type { Writable } from 'svelte/store';
	import { writable } from 'svelte/store';
	import { fade } from 'svelte/transition';

    let modalNewProductVisible = false;
    let modalViewProductVisible = false;

	export const nameToSearch = writable("");
	export const codeToSearch = writable("");
	export const products: Writable<Product[]> = writable([]);

    export const paginationData: Writable<Pagination> = writable();

	export const idToSearch = writable("");
	export const productSelected: Writable<ProductGet> = writable({ code: "", name: "", price: 0, vat_rate_id: 0 });

    function toogleModalNewProductVisible () {
        modalNewProductVisible = !modalNewProductVisible;
    }

    function toogleModalViewProductVisible () {
        modalViewProductVisible = !modalViewProductVisible;
    }

	//REQUEST FUNCTIONS
	async function loadProducts (url: string | undefined = '/api/products?page=1') {
        const res = await fetch(url,
            {
                headers: {'Accept': 'application/json'}
            }
        );
        
        $products = []
        const data: Pagination = await res.json();
        paginationData.set(data)
        if (Array.isArray(data.data)) {
            $products = data.data
        }
        //console.log(data.data)

        if (res.status === 401) {
            window.location.href = 'login';
        }
    };

	async function getProductById () {
        if (!$idToSearch) { return }

        const res = await fetch('/api/products/'+$idToSearch, {
            credentials: "include",
            headers: {
                'Accept': 'application/json'
            }
        })

        if (res.status === 200) {
            const product: ProductGet = await res.json()
    
            $productSelected = product;
            $productSelected.new = true;
        }

    }

	const requestFunctions = {
		loadProducts: loadProducts,
		getProductById: getProductById
	}

</script>

<div in:fade class="flex flex-col p-5 gap-5 w-fit h-full max-w-full">
	<h2 class="font-bold text-3xl text-center">Administración de Comprobantes</h2>
	<div class="flex flex-col gap-7">
		<SectionSearch nameToSearch={nameToSearch} codeToSearch={codeToSearch} requestFunctions={requestFunctions} />
		<!-- <SectionOptions toogleModalNewProductVisible={toogleModalNewProductVisible} requestFunctions={requestFunctions} /> -->
		<SectionList/>
	</div>
	<!-- <ModalNewProduct requestFunctions={requestFunctions} visible={modalNewProductVisible} toogleModalNewProductVisible={toogleModalNewProductVisible} />
	<ModalViewProduct requestFunctions={requestFunctions} visible={modalViewProductVisible} toogleModalViewProductVisible={toogleModalViewProductVisible} productSelected={productSelected} idToSearch={idToSearch} /> -->
</div>

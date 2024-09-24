<script lang="ts">
    import type { Product, ProductGet } from '$lib/interfaces/product';
	import ModalNewProduct from '$lib/sections/products/ModalNewProduct.svelte';
    import ModalViewProduct from '$lib/sections/products/ModalViewProduct.svelte';
	import SectionList from '$lib/sections/products/SectionList.svelte';
	import SectionOptions from '$lib/sections/products/SectionOptions.svelte';
	import SectionSearch from '$lib/sections/products/SectionSearch.svelte';
    import type { Writable } from 'svelte/store';
	import { writable } from 'svelte/store';
	import { fade } from 'svelte/transition';

    let modalNewProductVisible = false;
    let modalViewProductVisible = false;

	export const nameToSearch = writable("");
	export const codeToSearch = writable("");
	export const products: Writable<Product[]> = writable([]);

	export const idToSearch = writable("");
	export const productSelected: Writable<ProductGet> = writable({ code: "", name: "", price: 0, vat_rate_id: 0 });

    function toogleModalNewProductVisible () {
        modalNewProductVisible = !modalNewProductVisible;
    }

    function toogleModalViewProductVisible () {
        modalViewProductVisible = !modalViewProductVisible;
    }

	//REQUEST FUNCTIONS
	async function loadProducts () {
        const res = await fetch('/api/products',
            {
                headers: {'Accept': 'application/json'}
            }
        );
        
        $products = []
        const data = await res.json();
        if (Array.isArray(data)) {
            $products = data
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
        }

    }

	const requestFunctions = {
		loadProducts: loadProducts,
		getProductById: getProductById
	}

</script>

<div in:fade class="flex flex-col p-5 gap-5 w-fit max-w-full">
	<h2 class="font-bold text-3xl text-center">Mis productos y servicios</h2>
	<div class="flex flex-col gap-7">
		<SectionSearch products={products} nameToSearch={nameToSearch} codeToSearch={codeToSearch} />
		<SectionOptions toogleModalNewProductVisible={toogleModalNewProductVisible} requestFunctions={requestFunctions} />
		<SectionList products={products} requestFunctions={requestFunctions} toogleModalViewProductVisible={toogleModalViewProductVisible} productSelected={productSelected} idToSearch={idToSearch} />
	</div>
	<ModalNewProduct requestFunctions={requestFunctions} visible={modalNewProductVisible} toogleModalNewProductVisible={toogleModalNewProductVisible} />
	<ModalViewProduct requestFunctions={requestFunctions} visible={modalViewProductVisible} toogleModalViewProductVisible={toogleModalViewProductVisible} productSelected={productSelected} idToSearch={idToSearch} />
</div>

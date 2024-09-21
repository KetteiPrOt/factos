<script lang="ts">
    import type { Product } from '$lib/interfaces/product';
	import ModalNewProduct from '$lib/sections/products/ModalNewProduct.svelte';
	import SectionList from '$lib/sections/products/SectionList.svelte';
	import SectionOptions from '$lib/sections/products/SectionOptions.svelte';
	import SectionSearch from '$lib/sections/products/SectionSearch.svelte';
    import type { Writable } from 'svelte/store';
	import { writable } from 'svelte/store';
	import { fade } from 'svelte/transition';

    let modalVisible = false;

	export const nameToSearch = writable("");
	export const codeToSearch = writable("");
	export const products: Writable<Product[]> = writable([]);

    function toogleModalVisible () {
        modalVisible = !modalVisible;
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

	const requestFunctions = {
		loadProducts: loadProducts
	}

</script>

<div in:fade class="flex flex-col p-5 gap-5 w-fit max-w-full">
	<h2 class="font-bold text-3xl text-center">Mis productos y servicios</h2>
	<div class="flex flex-col gap-5">
		<SectionSearch products={products} nameToSearch={nameToSearch} codeToSearch={codeToSearch} />
		<SectionOptions toogleModalVisible={toogleModalVisible} />
		<SectionList products={products} requestFunctions={requestFunctions} />
	</div>
	<ModalNewProduct requestFunctions={requestFunctions} visible={modalVisible} toogleModalVisible={toogleModalVisible} />
</div>

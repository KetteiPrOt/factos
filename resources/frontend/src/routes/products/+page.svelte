<script lang="ts">
    import type { product } from '$lib/interfaces/product';
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
	export const products: Writable<product[]> = writable([]);

    function toogleModalVisible () {
        modalVisible = !modalVisible;
    }

</script>

<div in:fade class="flex flex-col p-5 gap-5 w-fit max-w-full">
	<h2 class="font-bold text-3xl text-center">Mis productos y servicios</h2>
	<div class="flex flex-col gap-5">
		<SectionSearch products={products} nameToSearch={nameToSearch} codeToSearch={codeToSearch} />
		<SectionOptions toogleModalVisible={toogleModalVisible} />
		<SectionList products={products} />
	</div>
	<ModalNewProduct visible={modalVisible} toogleModalVisible={toogleModalVisible} />
</div>

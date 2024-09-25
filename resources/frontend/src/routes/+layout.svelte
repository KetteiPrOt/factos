<script lang="ts">
	import '../app.css';
	import NavBar from '../lib/components/NavBar.svelte';
	import "@fontsource/chakra-petch";
	import { base } from '$app/paths';
    import { onMount } from 'svelte';

	let showNavBar = false; 

	onMount(()=>{
		if (window.location.pathname != base+'/login') {
			showNavBar = true;
		}
	})
</script>

<div class="app">
	{#if showNavBar}
	<NavBar />
	{/if}

	<main class="flex place-content-center max-w-full mt-[72px]">
		<slot />
	</main>

</div>

<style>
	* {
		font-family: 'Chakra Petch';
	}
</style>
<script lang="ts">
	import '../app.css';
	import NavBar from '../lib/components/NavBar.svelte';
	import "@fontsource/chakra-petch";
	import { base } from '$app/paths';
    import { onMount } from 'svelte';
    import { page } from '$app/stores';

	$: actualRoute = $page.route.id;
	$: showNavBar = actualRoute != '/login' ? true : false; 
	console.log(actualRoute)
	
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
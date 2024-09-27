<script lang="ts">
	import '../app.css';
	import NavBar from '../lib/components/NavBar.svelte';
	import "@fontsource/chakra-petch";
    import { page } from '$app/stores';

	$: actualRoute = $page.route.id;
	$: showNavBar = actualRoute != '/login' ? true : false; 
	
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
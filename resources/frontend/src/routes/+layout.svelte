<script lang="ts">
	import '../app.css';
	import NavBar from '../lib/components/NavBar.svelte';
	import "@fontsource/chakra-petch";
    import { page } from '$app/stores';
    import { goto } from '$app/navigation';

	$: actualRoute = $page.route.id;
	$: showNavBar = actualRoute != '/login' ? true : false;

	async function checkUser () {
		try {
			const resUser = await fetch('/api/user');
			const data = await resUser.json()
	
			if (resUser.status != 200) {
				goto('login')
			}
		} catch (error) {
			goto('login')
		}
	}

	$: {
		actualRoute;
		if (typeof window != 'undefined') {
			checkUser();

		}
	}
	
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
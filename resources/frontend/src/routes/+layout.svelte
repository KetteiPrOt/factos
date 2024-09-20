<script lang="ts">
	import '../app.css';
	import NavBar from '../NavBar.svelte';
</script>

<div class="app">
	<NavBar />

	<main class="flex place-content-center max-w-full mt-[72px]">
		<slot />
	</main>

</div>
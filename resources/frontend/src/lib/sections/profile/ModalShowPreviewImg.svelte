<script lang="ts">
	import { blur, fade, slide } from "svelte/transition";
    export let visible: boolean;
    export let toogleModalVisible: ()=>void;
    export let imgPreviewUrl: string
    export let imgActualUrl: string

</script>

{#if visible && (imgActualUrl || imgPreviewUrl)}
<button transition:blur class="fixed top-0 left-0 z-50 flex flex-col bg-blue-700/60 backdrop-blur w-full h-dvh" on:click={toogleModalVisible}>
    <button class="m-auto flex flex-col place-items-center gap-2 max-h-dvh p-4">
        <img src={imgPreviewUrl ? imgPreviewUrl : imgActualUrl} alt="Preview img" class="rounded-md max-h-full object-contain">
    </button>
</button>
{/if}
<script lang="ts">
	import { Icon } from "svelte-icons-pack";
	import { AiOutlinePlus } from "svelte-icons-pack/ai";

    export let toogleModalNewEstabVisible: ()=>void;

</script>
<div class="flex flex-col gap-4 justify-center pt-7">
    <section class="flex flex-wrap gap-10 justify-center">
        <button class="flex flex-row gap-2 place-items-center bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600 text-slate-50" on:click={toogleModalNewEstabVisible}>
            <Icon src={AiOutlinePlus} />
            <span>Nuevo</span>
        </button>
    </section>
</div>
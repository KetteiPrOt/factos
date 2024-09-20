<script lang="ts">
	import { Icon } from "svelte-icons-pack";
	import { AiOutlinePlus, AiOutlineSearch } from "svelte-icons-pack/ai";
    import {BiTrash} from "svelte-icons-pack/bi"

    export let toogleModalVisible: ()=>void;

</script>
<div class="flex flex-col gap-4 justify-center border-t border-[--color-border] pt-7">
    <section class="flex flex-row gap-10 justify-center">
        <button class="flex flex-row gap-2 place-items-center bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600 text-slate-50" on:click={toogleModalVisible}>
            <Icon src={AiOutlinePlus} />
            <span>Nuevo</span>
        </button>
        <button class="flex flex-row gap-2 place-items-center bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600 text-slate-50">
            <Icon src={BiTrash} />
            <span>Eliminar todos los productos</span>
        </button>
    </section>
</div>
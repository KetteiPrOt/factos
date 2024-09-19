<script lang="ts">
	import { blur } from "svelte/transition";

    export let visible: boolean;
    export let toogleModalVisible: ()=>void;
</script>

{#if visible}
<button transition:blur class="fixed top-0 left-0 flex bg-blue-700/60 backdrop-blur w-full h-dvh" on:click={toogleModalVisible}>
    <button class="m-auto flex flex-col gap-5 p-6 border border-[--color-border] bg-slate-100/80 rounded-md hover:cursor-default" on:click={(e)=>{e.stopPropagation()}}>
        <section>
            <h3 class=" font-medium text-lg">
                Nuevo producto
            </h3>
        </section>
        <section class="flex flex-col gap-3">
            <div class="flex flex-row gap-5">
                <label for="codigo">Código:</label>
                <input name="codigo" class="border border-[--color-border] bg-transparent rounded-md px-1 ml-auto place-self-center" type="text">
            </div>
            <div class="flex flex-row gap-5">
                <label for="nombre">Nombre:</label>
                <input name="nombre" class="border border-[--color-border] bg-transparent rounded-md px-1 ml-auto place-self-center" type="text">
            </div>
            <div class="flex flex-row gap-5">
                <label for="precio">Precio:</label>
                <input name="precio" class="border border-[--color-border] bg-transparent rounded-md px-1 ml-auto place-self-center" type="number">
            </div>
            <div class="flex flex-row gap-5">
                <label for="tarifa_iva">Tarifa IVA:</label>
                <input name="tarifa_iva" class="border border-[--color-border] bg-transparent rounded-md px-1 ml-auto place-self-center" type="number">
            </div>
        </section>
        <section class="flex flex-row gap-5 self-start text-slate-50">
            <button class="h-[34px] bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600">
                Guardar
            </button>
            <button class="h-[34px] box-border border border-[--color-theme-1] text-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-slate-200" on:click={toogleModalVisible}>
                Cancelar
            </button>
        </section>
    </button>
</button>
{/if}
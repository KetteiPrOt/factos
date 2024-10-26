<script lang="ts">
    import { slide } from "svelte/transition";
    import ModalShowPreviewImg from "./ModalShowPreviewImg.svelte";

    let imgPreviewUrl = "";
    let modalVisible = false;

    function toogleModalVisible () {
        modalVisible = !modalVisible;
    }

    function showPreview () {
        toogleModalVisible()
    }

    function setImgPreviewUrl (e: Event) {
        const input = e.target as HTMLInputElement;
        let image: File | null = null; 
        if (input.files) {
            image = input.files[0]
        }

        if (image) {
            const reader = new FileReader();
            reader.onload = () => {
                imgPreviewUrl = reader.result as string
            };
            reader.readAsDataURL(image);
        }

    }
</script>

<div class="flex flex-col border border-[--color-border] p-4 rounded-lg gap-4 w-fit max-w-[405px] place-self-center self-start">
    <section class="bg-[--color-theme-1] rounded-md">
        <h3 class="font-medium text-center text-slate-50 p-1.5">Perfil</h3>
    </section>
    <section class="flex flex-col gap-2">
        <div class="flex flex-col gap-2">
            <label for="name">Nombre:</label>
            <input name="name" class="border border-[--color-border] bg-transparent rounded-md px-1" type="text">
        </div>
        <div class="flex flex-col gap-1">
            <label for="email">Email:</label>
            <input name="email" class="border border-[--color-border] bg-transparent rounded-md px-1" type="text">
        </div>
        <div class="flex flex-col gap-1">
            <label for="logo">Logo de emisor:</label>
            <input name="logo" class="" type="file" accept="image/*" on:change={(e)=>setImgPreviewUrl(e)}>
        </div>
        <div>
            {#if imgPreviewUrl}
            <div in:slide={{axis: 'y'}} class="border border-[--color-border] bg-transparent rounded-md overflow-hidden">
                <button class="rounded-md px-2 py-1 w-full" on:click={showPreview}>
                    Vista previa
                </button>
            </div>
            {/if}
        </div>
        <div class="flex flex-col gap-1">
            <label for="ruc">RUC:</label>
            <input name="ruc" class="border border-[--color-border] bg-transparent rounded-md px-1" type="text">
        </div>
        <div class="flex flex-col gap-1">
            <label for="matriz_address">Dirección matriz:</label>
            <input name="matriz_address" class="border border-[--color-border] bg-transparent rounded-md px-1" type="text">
        </div>
        <div class="flex flex-row text-slate-50 mt-4">
            <button class="bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600">
                Guardar
            </button>
        </div>
    </section>
    <ModalShowPreviewImg visible={modalVisible} toogleModalVisible={toogleModalVisible} imgUrl={imgPreviewUrl} />
</div>
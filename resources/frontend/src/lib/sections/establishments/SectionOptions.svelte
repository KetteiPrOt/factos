<script lang="ts">
	import { Icon } from "svelte-icons-pack";
	import { AiOutlinePlus, AiOutlineSearch } from "svelte-icons-pack/ai";
    import {BiTrash} from "svelte-icons-pack/bi"
    import { slide } from "svelte/transition";

    export let toogleModalNewEstabVisible: ()=>void;
    export let requestFunctions: {
        loadEstablishments: (url?: string | undefined) => Promise<void>;
        getEstablishmentsById: () => Promise<void>;
    }

    let confirmDeleteVisible = false;

    function showConfirmDelete () {
        confirmDeleteVisible = true;
    }

    function hiddenConfirmDelete () {
        confirmDeleteVisible = false;
    }

    function getCookies () {
        let cookies = document.cookie.split(";");
        let dict: {[key:string]: string} = {};
        cookies.forEach(cookie => {
            let [key, value] = cookie.split("=");
            key = key.trim();
            value = decodeURIComponent(value);
            dict[key] = value;
        })

        return dict
    }

    async function delteAllEstablishments () {
        const res = await fetch('/api/establishments', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN']
            },
            method: "DELETE",
            credentials: "include"
        });

        if (res.status === 200) {
            hiddenConfirmDelete()
            requestFunctions.loadEstablishments();
        }
    }

</script>
<div class="flex flex-col gap-4 justify-center border-t border-[--color-border] pt-7">
    <section class="flex flex-wrap gap-10 justify-center">
        <button class="flex flex-row gap-2 place-items-center bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600 text-slate-50" on:click={toogleModalNewEstabVisible}>
            <Icon src={AiOutlinePlus} />
            <span>Nuevo</span>
        </button>
        <div class="relative">
            <button class="flex flex-row gap-2 place-items-center bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600 text-slate-50" on:click={showConfirmDelete}>
                <Icon src={BiTrash} />
                <span>Eliminar todos los productos</span>
            </button>
            {#if confirmDeleteVisible}
            <div transition:slide={{axis: "y"}} class="absolute mt-1 bg-blue-700/60 backdrop-blur-sm w-full flex flex-row gap-1 p-1 text-slate-50 rounded-md">
                <button class="flex-grow border rounded shadow-sm shadow-slate-600 hover:shadow hover:shadow-slate-900 hover:bg-blue-700/30" on:click={delteAllEstablishments}>
                    Confirmar
                </button>
                <button class="flex-grow bg-slate-50 text-[--color-theme-1] rounded shadow-sm shadow-slate-600 hover:shadow hover:shadow-slate-900 hover:bg-slate-200" on:click={hiddenConfirmDelete}>
                    Cancelar
                </button>
            </div>
            {/if}
        </div>
    </section>
</div>
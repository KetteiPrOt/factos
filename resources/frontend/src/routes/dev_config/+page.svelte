<script lang="ts">
	import { blur, fade } from "svelte/transition";
	import { api_rest, url_api } from "$lib/global_stores/config";
	import { get } from "svelte/store";
	import { persistent } from "$lib/global_stores/localStorage";


    function updateURLServer (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;

        $url_api = value;
        console.log($url_api);
    }

    function toogleApiRestMode () {
        api_rest.update((value) => {
            if (value === "on") {
                return "off"
            } else {
                return "on"
            }
        })
        // const value = get(api_rest);
        // if (value === "on") {
        //     api_rest.set("off")
        // } else {
        //     api_rest.set("on")
        // }
    }



</script>


<div in:fade class="flex flex-col p-5 gap-5 w-fit max-w-full ">
    <h2 class="font-bold text-3xl text-center">
        Dev Config
    </h2>
    <section class="flex flex-col place-items-center">
        <h3>API REST</h3>
        <button class="flex flex-col border border-[--color-border] rounded-full p-1.5 w-10" on:click={toogleApiRestMode}>
            <div class="w-3 h-3 bg-[--color-border] rounded-full {$api_rest === 'on' ? 'self-end bg-[--color-theme-1]' : 'self-start'}">

            </div>
        </button>
    </section>
    {#if $api_rest === "on"}
    <section transition:blur class="flex flex-col gap-3">
        <div class="flex flex-col gap-1 text-center">
            <label for="establecimiento">
               URL API 
            </label>
            <input name="establecimiento" class="border border-[--color-border] bg-transparent rounded-md px-1 ml-auto place-self-center" type="text" value={$url_api} on:input={(e)=>updateURLServer(e)}>
        </div>

    </section>
    {/if}
</div>
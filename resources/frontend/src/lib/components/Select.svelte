<script lang="ts">
    import { writable, type Writable } from "svelte/store";
    import { slide } from "svelte/transition";

    export let options: {id: number, name: string}[];
    // export let updateIceId: (id: number)=>void;
    export let optionSelected: Writable<number>;
    export let alert: boolean;
    export let pos: "up" | "down" = "up";
    export let wFull: boolean = false;
    
    let isOpen = false;

    let nameSelected : string | undefined;

    $: {
        if (options) {
            nameSelected = options.find((opt)=>{ 
                return opt.id === $optionSelected
            })?.name
        }
    }


    function toogleOpen () {
        isOpen = !isOpen;
    }

    function selectThisOpt (id: number) {
        $optionSelected = id;
        // updateIceId(id);
        toogleOpen();
    }
    
</script>

<button class="relative box-border border {alert ? 'border-red-500' : 'border-[--color-border]'} bg-transparent rounded-md px-1 ml-auto h-[26px] {wFull ? 'w-full' : 'w-[205px]'} place-self-center" >
    <button class="w-full h-full" on:click={toogleOpen}>
        <span class="line-clamp-1">
            {nameSelected ? nameSelected : '-- Seleccionar --'}
        </span>
    </button>
    {#if isOpen}
    <div transition:slide={{axis: "y"}} class="absolute flex flex-col gap-1 p-1 bg-slate-100 border shadow-md w-80 max-h-[300px] z-30 overflow-y-auto rounded-md {pos === "up" ? 'bottom-[27px]' : 'top-[27px]'} left-[-2px]">
        <button class="hover:bg-blue-500/70" on:click={()=>selectThisOpt(0)}>
            Ninguno
        </button>
        {#if options}
        {#each options as option}
        <button class=" {$optionSelected === option.id ? 'bg-blue-500/90 hover:bg-blue-500' : 'hover:bg-blue-500/70'}" on:click|stopPropagation={()=>selectThisOpt(option.id)}>
            {option.name}
        </button>
        {/each}
        {/if}
    </div>
    {/if}
</button>
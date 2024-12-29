<script lang="ts">
    import { writable, type Writable } from "svelte/store";
    import { slide } from "svelte/transition";

    export let options: {id: number, name: string}[];
    // export let updateIceId: (id: number)=>void;
    export let optionSelected: Writable<number>;
    // export let alert: boolean = false;
    export let pos: "up" | "down" = "down";
    // export let wFull: boolean = false;
    
    let isOpen = true;

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

{#if options.length > 0}
<div in:slide={{axis: "y"}} class="absolute flex flex-col gap-1 p-1 bg-slate-100 border shadow-md w-80  z-30 overflow-y-auto rounded-md {pos === "up" ? 'bottom-[27px]' : 'top-[27px]'} left-[-2px]">
    {#each options as option}
    <button class=" {$optionSelected === option.id ? 'bg-blue-500/90 hover:bg-blue-500' : 'hover:bg-blue-500/70'}" on:click|stopPropagation={()=>selectThisOpt(option.id)}>
        {option.name}
    </button>
    {/each}
</div>
{/if}
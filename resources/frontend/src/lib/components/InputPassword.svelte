<script lang="ts">
    import { Icon } from 'svelte-icons-pack';
    import { AiOutlineEye, AiOutlineEyeInvisible } from 'svelte-icons-pack/ai';
    import { writable, type Writable } from 'svelte/store';
    import { scale } from 'svelte/transition';

    export let name: string = '';
    export let tailwindClass: string = '';
    export let alert: boolean = false;
    export let updatePass: (e: Event) => void = (e) => {};
    export let keyPress: (e: KeyboardEvent) => void = (e) => {};
    export let bindInput: Writable<HTMLInputElement> = writable();
    
    export let focusInputPass: Writable<boolean> = writable(false);
    
    let showPass = false; 

    function toogleShowPass () {
        showPass = !showPass;
        if ($bindInput) {
            $bindInput.focus()
        }
    }

    $: {
        if ($focusInputPass) {
            if ($bindInput) {
                $bindInput.focus()
                focusInputPass.set(false);
            }
        }
    }

</script>

<div class="flex flex-row px-1 border {alert ? 'border-red-500' : 'border-[--color-border]'} min-h-[26px] bg-transparent rounded-md {tailwindClass}">    
    <input bind:this={$bindInput} name={name} type="{showPass ? 'text' : 'password'}" class="bg-transparent pr-1 outline-none flex-grow" on:input={(e)=>updatePass(e)} on:keypress={(e)=>keyPress(e)} aria-autocomplete='none'>
    <button on:click={toogleShowPass} class="outline-none">
        {#if showPass}
            <div in:scale>
                <Icon src={AiOutlineEye} size={20} />
            </div>
        {:else}    
            <div in:scale>
                <Icon src={AiOutlineEyeInvisible} size={20} />
            </div>
        {/if}
    </button>
</div>
<script lang="ts">
	import { Icon } from "svelte-icons-pack";
	import { AiFillCaretDown, AiOutlineClose, AiOutlineMenu } from "svelte-icons-pack/ai";
    import { slide, scale } from "svelte/transition";

    import { base } from "$app/paths";

    //Visibility
    let lateralBarVisible = false;
    let dropConfVisible = false;
    let dropEmisVisible = false;

    //Toggle visibility
    function toogleLBarVisible () {
        lateralBarVisible = !lateralBarVisible;
    }
    function closeLBarVisible () {
        lateralBarVisible = false;
        closeAllDrops();
    }
    function closeAllDrops (exc?: string) {
        if (exc != "config") {
            dropConfVisible = false;
        }
        if (exc != "emition") {
            dropEmisVisible = false;
        }
    }
    function toogleDropConfVisible () {
        closeAllDrops("config");
        dropConfVisible = !dropConfVisible;
    }
    function toogleDropEmisVisible () {
        closeAllDrops("emition");
        dropEmisVisible = !dropEmisVisible;
    }

</script>

<nav class="fixed top-0 w-full flex flex-row gap-4 bg-[--color-theme-1] text-slate-50 place-items-center p-4">
    <div>
        <a class=" font-semibold text-4xl" href="{base}/home" on:click={closeLBarVisible}>
            Factos
        </a>
    </div>
    <div class=" ml-auto hidden sm:block">
        <ul class="flex flex-row gap-1">
            <li class="relative">
                <button class="flex flex-wrap max-h-[32px] overflow-y-hidden justify-center place-items-center gap-1 p-1 hover:border-b" on:click={toogleDropConfVisible}>
                    <span>Configuración</span>
                    <Icon src={AiFillCaretDown} />
                </button>
                {#if dropConfVisible}
                <div transition:slide={{axis: "y"}} class="absolute top-[32px] bg-slate-50/70 backdrop-blur rounded-b-md p-1 text-[--color-theme-1] font-medium text-center">
                    <ul>
                        <li class="border-b border-transparent hover:border-[--color-theme-1]">
                            <button class="block w-full">
                                Datos emisor
                            </button>
                        </li>
                        <li class="border-b border-transparent hover:border-[--color-theme-1]">
                            <a class="block" href="{base}/products" on:click={()=>closeAllDrops()}>
                                Productos y servicios
                            </a>
                        </li>
                        <li class="border-b border-transparent hover:border-[--color-theme-1]">
                            <button class="block w-full">
                                Puntos de emisión
                            </button>
                        </li>
                        <li class="border-b border-transparent hover:border-[--color-theme-1]">
                            <a class="block" href="{base}/dev_config" on:click={()=>closeAllDrops()}>
                                Dev Config
                            </a>
                        </li>
                    </ul>
                </div>
                {/if}
            </li>
            <li class="relative">
                <button class="flex flex-wrap max-h-[32px] overflow-y-hidden justify-center place-items-center gap-1 p-1 hover:border-b" on:click={toogleDropEmisVisible}>
                    <span>Emisión</span>
                    <Icon src={AiFillCaretDown} />
                </button>
                {#if dropEmisVisible}
                <div transition:slide={{axis: "y"}} class="absolute top-[32px] w-full bg-slate-50/70 backdrop-blur rounded-b-md p-1 text-[--color-theme-1] font-medium text-center">
                    <ul>
                        <li class="border-b border-transparent hover:border-[--color-theme-1]">
                            <a class="block" href="{base}/home" on:click={()=>closeAllDrops()}>
                                Factura
                            </a>
                        </li>
                    </ul>
                </div>
                {/if}
            </li>
            <li>
                <button class="flex flex-wrap max-h-[32px] overflow-y-hidden justify-center place-items-center gap-1 p-1 hover:border-b">
                    <span>Comprobantes</span>
                    <Icon src={AiFillCaretDown} />
                </button> 
            </li>
        </ul>

    </div>
    
    <div class="sm:flex flex-row gap-3 hidden">
        
        <button class="border p-1.5 rounded-md hover:bg-slate-700/30">
            Perfil
        </button>
        
        <button class="bg-slate-50 text-[--color-theme-1] p-1.5 rounded-md hover:bg-slate-50/80">
            Login
        </button>
        
    </div>

    <button class=" sm:hidden ml-auto rounded-md p-1 hover:outline outline-1" on:click={toogleLBarVisible}>
        <!-- <Icon src={lateralBarVisible ? AiOutlineClose : AiOutlineMenu} size={30} /> -->
        {#if lateralBarVisible}
            <div in:scale>
                <Icon src={AiOutlineClose} size={30} /> 
            </div>
        {:else}
            <div in:scale>
                <Icon src={AiOutlineMenu} size={30} />
            </div>
        {/if}
        
    </button>
</nav>

{#if lateralBarVisible}
    <div transition:slide={{axis: "x"}} class="fixed h-[calc(100dvh-72px)] w-3/6 flex flex-col p-4 gap-4 bg-blue-700/50 backdrop-blur-sm sm:hidden text-slate-50">
        <div class="flex justify-center text-center">
            <ul class="flex flex-col gap-1 place-items-center">
                <li class="relative">
                    <button class="flex flex-wrap max-h-[32px] overflow-y-hidden justify-center place-items-center gap-1 p-1 hover:border-b" on:click={toogleDropConfVisible}>
                        <span>Configuración</span>
                        <Icon src={AiFillCaretDown} />
                    </button>
                    {#if dropConfVisible}
                    <div transition:slide={{axis: "y"}} class="absolute top-[32px] bg-slate-50/70 backdrop-blur  rounded-b-md p-1 text-[--color-theme-1] font-medium text-center z-10">
                        <ul>
                            <li class="border-b border-transparent hover:border-[--color-theme-1]">
                                <button class="block w-full">
                                    Datos emisor
                                </button>
                            </li>
                            <li class="border-b border-transparent hover:border-[--color-theme-1]">
                                <a class="block" href="{base}/products" on:click={closeLBarVisible}>
                                    Productos y servicios
                                </a>
                            </li>
                            <li class="border-b border-transparent hover:border-[--color-theme-1]">
                                <button class="block w-full" >
                                    Puntos de emisión
                                </button>
                            </li>
                            <li class="border-b border-transparent hover:border-[--color-theme-1]">
                                <a class="block" href="{base}/dev_config" on:click={closeLBarVisible}>
                                    Dev Config
                                </a>
                            </li>
                        </ul>
                    </div>
                    {/if}
                </li>
                <li class="relative">
                    <button class="flex flex-wrap max-h-[32px] overflow-y-hidden justify-center place-items-center gap-1 p-1 hover:border-b" on:click={toogleDropEmisVisible}>
                        <span>Emisión</span>
                        <Icon src={AiFillCaretDown} />
                    </button>
                    {#if dropEmisVisible}
                    <div transition:slide={{axis: "y"}} class="absolute top-[32px] w-full bg-slate-50/70 backdrop-blur  rounded-b-md p-1 text-[--color-theme-1] font-medium text-center z-10">
                        <ul>
                            <li class="border-b border-transparent hover:border-[--color-theme-1]">
                                <a class="block" href="{base}/home" on:click={closeLBarVisible}>
                                    Factura
                                </a>
                            </li>
                        </ul>
                    </div>
                    {/if}
                </li>
                <li>
                    <button class="flex flex-wrap max-h-[32px] overflow-y-hidden justify-center place-items-center gap-1 p-1 hover:border-b">
                        <span>Comprobantes</span>
                        <Icon src={AiFillCaretDown} />
                    </button> 
                </li>
            </ul>
    
        </div>
        
        <div class="mt-auto py-3 flex flex-row gap-3 justify-center">
            
            <button class="border p-1.5 rounded-md hover:bg-slate-700/30">
                Perfil
            </button>
            
            <button class="bg-slate-50 text-[--color-theme-1] p-1.5 rounded-md hover:bg-slate-50/80">
                Login
            </button>
            
        </div>          
    </div>
{/if}
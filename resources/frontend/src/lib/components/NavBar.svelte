<script lang="ts">
	import { Icon } from "svelte-icons-pack";
	import { AiFillCaretDown, AiOutlineClose, AiOutlineMenu } from "svelte-icons-pack/ai";
    import { slide, scale } from "svelte/transition";

    import { base } from "$app/paths";
    import { goto } from "$app/navigation";

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

    async function logout () {
        const res = await fetch('/api/logout', {
            headers: {
                'Accept': 'application/json',
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN'],
                'Referer': window.location.href,
                'Origin': window.location.origin
            },
            method: "POST",
            credentials: "include"
        })

        if (res.status === 200) {
            goto('login')
        }
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

</script>

<nav class="fixed z-40 top-0 w-full flex flex-row gap-4 bg-[--color-theme-1] text-slate-50 place-items-center p-4">
    <div>
        <a class=" font-semibold text-4xl" href="home" on:click={closeLBarVisible}>
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
                            <a class="block" href="products" on:click={()=>closeAllDrops()}>
                                Productos y servicios
                            </a>
                        </li>
                        <li class="border-b border-transparent hover:border-[--color-theme-1]">
                            <a class="block" href="establishments" on:click={()=>closeAllDrops()}>
                                Establecimientos
                            </a>
                        </li>
                        <li class="border-b border-transparent hover:border-[--color-theme-1]">
                            <a class="block" href="issuance_points" on:click={()=>closeAllDrops()}>
                                Puntos de emisión
                            </a>
                        </li>
                        <li class="border-b border-transparent hover:border-[--color-theme-1]">
                            <a class="block" href="persons" on:click={()=>closeAllDrops()}>
                                Clientes
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
                            <a class="block" href="invoices" on:click={()=>closeAllDrops()}>
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
        
        <button class="border p-1.5 rounded-md hover:bg-slate-700/30" on:click={()=>goto('profile')}>
            Perfil
        </button>
        
        <button class="bg-slate-50 text-[--color-theme-1] p-1.5 rounded-md hover:bg-slate-50/80" on:click={logout}>
            Logout
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
    <div transition:slide={{axis: "x"}} class="fixed z-20 h-[calc(100dvh-72px)] w-3/6 flex flex-col p-4 gap-4 bg-blue-700/50 backdrop-blur-sm sm:hidden text-slate-50">
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
                                <a class="block" href="products" on:click={closeLBarVisible}>
                                    Productos y servicios
                                </a>
                            </li>
                            <li class="border-b border-transparent hover:border-[--color-theme-1]">
                                <a class="block" href="establishments" on:click={closeLBarVisible}>
                                    Establecimientos
                                </a>
                            </li>
                            <li class="border-b border-transparent hover:border-[--color-theme-1]">
                                <a class="block" href="issuance_points" on:click={closeLBarVisible}>
                                    Puntos de emisión
                                </a>
                            </li>
                            <li class="border-b border-transparent hover:border-[--color-theme-1]">
                                <a class="block" href="persons" on:click={closeLBarVisible}>
                                    Clientes
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
                                <a class="block" href="invoices" on:click={closeLBarVisible}>
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
            
            <button class="bg-slate-50 text-[--color-theme-1] p-1.5 rounded-md hover:bg-slate-50/80" on:click={logout}>
                Logout
            </button>
            
        </div>          
    </div>
{/if}
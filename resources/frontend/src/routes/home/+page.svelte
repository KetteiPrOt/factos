<script lang="ts">

    import { Icon } from "svelte-icons-pack";
    import { AiFillAccountBook, AiFillCreditCard } from "svelte-icons-pack/ai";
    import { BiCreditCard, BiCreditCardAlt, BiNote, BiSolidCreditCard, BiPackage } from "svelte-icons-pack/bi";
    import { BsBoxes } from "svelte-icons-pack/bs"
    import { fade } from "svelte/transition";
    import { goto } from "$app/navigation";


</script>

<div in:fade>
    <div class="relative flex flex-col gap-10 h-fit w-fit max-w-fit mx-auto mt-10 px-2">
        <section class="border-b border-[--color-theme-1]">
            <h1 class="text-6xl text-[--color-theme-1] font-bold tracking-widest p-5 text-center">
                FACTOS
            </h1>
        </section>
        <section class="flex flex-wrap gap-5 justify-center">
            <button class="flex flex-col gap-1 place-items-center bg-[--color-theme-1] w-[180.5px] p-5 rounded-md shadow-sm text-slate-50 shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={()=>goto('invoices')}>
                <Icon src={BiSolidCreditCard} size={60}/>
                <span class=" text-3xl">
                    Factura
                </span>
            </button>
            <button class="flex flex-col gap-1 place-items-center bg-[--color-theme-1] w-[180.5px] p-5 rounded-md shadow-sm text-slate-50 shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={()=>goto('products')}>
                <Icon src={BiPackage} size={65}/>
                <span class=" text-3xl">
                    Productos
                </span>
            </button>
        </section>
    </div>
</div>
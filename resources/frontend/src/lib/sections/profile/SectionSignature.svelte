<script lang="ts">
    import InputPassword from "$lib/components/InputPassword.svelte";

    function updatePass (e: Event) {
        const input = e.target as HTMLInputElement;
        const value = input.value;
        console.log(value)
    }
    
</script>

<div class="flex flex-col border border-[--color-border] p-4 rounded-lg gap-4 w-fit max-w-[405px] place-self-center">
    <section class="bg-[--color-theme-1] rounded-md">
        <h3 class="font-medium text-center text-slate-50 p-1.5">Firma</h3>
    </section>
    <section class="flex flex-col gap-2">
        <div class="flex flex-col gap-2">
            <label for="cert">Certificado:</label>
            <input name="cert" class="" type="file">
        </div>
        <div class="flex flex-col gap-1">
            <label for="password">Contraseña:</label>
            <InputPassword name='password' tailwindClass={'w-full'} updatePass={updatePass} />
        </div>
        <div class="flex flex-col gap-1">
            <label for="effective_date">Fecha vigencia:</label>
            <input name="effective_date" class="border border-[--color-border] bg-transparent rounded-md px-1" type="date" disabled>
        </div>
        <div class="flex flex-col gap-1">
            <label for="owner">RUC:</label>
            <input name="owner" class="border border-[--color-border] bg-transparent rounded-md px-1" type="text" disabled>
        </div>
        <div class="flex flex-row text-slate-50 mt-4">
            <button class="bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600">
                Cargar
            </button>
        </div>
    </section>
</div>
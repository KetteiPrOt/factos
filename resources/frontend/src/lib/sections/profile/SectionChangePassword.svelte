<script lang="ts">
    import InputPassword from "$lib/components/InputPassword.svelte";

    
</script>

<div class="flex flex-col border border-[--color-border] p-4 rounded-lg gap-4 w-full max-w-[405px] place-self-center">
    <section class="bg-[--color-theme-1] rounded-md">
        <h3 class="font-medium text-center text-slate-50 p-1.5">Cambiar contraseña</h3>
    </section>
    <section class="flex flex-col gap-2">
        <div class="flex flex-col gap-2">
            <label for="current_password">Contraseña actual:</label>
            <InputPassword name="current_pasword" />
        </div>
        <div class="flex flex-col gap-1">
            <label for="new_password">Nueva contraseña:</label>
            <InputPassword name="new_pasword" />
        </div>
        <div class="flex flex-col gap-1">
            <label for="confirm_password">Confirmar contraseña:</label>
            <InputPassword name="confirm_pasword" />
        </div>
        <div class="flex flex-row text-slate-50 mt-4">
            <button class="bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600">
                Guardar
            </button>
        </div>
    </section>
</div>
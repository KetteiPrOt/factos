<div class="flex flex-col border border-[--color-border] p-4 rounded-lg gap-4 box-border w-full max-w-full place-self-center">
    <section class="bg-[--color-theme-1] rounded-md">
        <h3 class="font-medium text-center text-slate-50 p-1.5">Campos adicionales</h3>
    </section>
    <section class="max-w-full">
        <div class="block w-full  rounded-md max-w-full overflow-x-auto">
            <table class="max-w-full w-full border-collapse text-center rounded-md">
                <thead class="bg-[--color-theme-1]">
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Nombre</td>
                        <td>Descripción</td>
                        <td>Acciones</td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
    </section>
    <section class="flex flex-row gap-3 text-slate-50">
        <button class="bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600">
            + Añadir campo adicional 
        </button>
    </section>
</div>

<style>
    th {
        border: .5px solid var(--color-border);
        color: #f8fafc;
        padding: .5rem;
    }
    td {
        border: .5px solid var(--color-border);
        padding: .5rem;
    }
</style>
<script>
	import { Icon } from "svelte-icons-pack";
	import { AiOutlineSearch } from "svelte-icons-pack/ai";

</script>
<div class="flex flex-col border border-[--color-border] p-4 rounded-lg gap-4 box-border w-fit max-w-full place-self-center">
    <section class="bg-[--color-theme-1] rounded-md">
        <h3 class="font-medium text-center text-slate-50 p-1.5">Detalles</h3>
    </section>
    <section class="flex flex-row gap-3 place-items-center">
        <span>Código / Descripción</span>
        <div class="flex flex-row place-items-center border border-[--color-border] rounded-md overflow-hidden">
            <input name="identificacion" class=" bg-transparent px-1" type="text" placeholder="Search">
            <button class="p-1 hover:bg-[--color-theme-1] hover:text-slate-50">
                <Icon src={AiOutlineSearch} size={20} />
            </button>
        </div>
    </section>
    <section class="max-w-full">
        <div class="block w-full  rounded-md max-w-full overflow-x-auto">
            <table class="max-w-full border-collapse text-center">
                <thead class="bg-[--color-theme-1]">
                    <tr>
                        <th>Código</th>
                        <th>Cantidad</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Precio</th>
                        <th>Tarifa</th>
                        <th>Descuento</th>
                        <th>Valor Total</th>
                        <th>Valor ICE</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Código</td>
                        <td>Cantidad</td>
                        <td>Descripción</td>
                        <td>Precio</td>
                        <td>Precio</td>
                        <td>Tarifa</td>
                        <td>Descuento</td>
                        <td>Valor Total</td>
                        <td>Valor ICE</td>
                        <td>Acciones</td>
                    </tr>
                    <tr>
                        <td>Código</td>
                        <td>Cantidad</td>
                        <td>Descripción</td>
                        <td>Precio</td>
                        <td>Precio</td>
                        <td>Tarifa</td>
                        <td>Descuento</td>
                        <td>Valor Total</td>
                        <td>Valor ICE</td>
                        <td>Acciones</td>
                    </tr>
                    <tr>
                        <td>Código</td>
                        <td>Cantidad</td>
                        <td>Descripción</td>
                        <td>Precio</td>
                        <td>Precio</td>
                        <td>Tarifa</td>
                        <td>Descuento</td>
                        <td>Valor Total</td>
                        <td>Valor ICE</td>
                        <td>Acciones</td>
                    </tr>
                    <tr>
                        <td>Código</td>
                        <td>Cantidad</td>
                        <td>Descripción</td>
                        <td>Precio</td>
                        <td>Precio</td>
                        <td>Tarifa</td>
                        <td>Descuento</td>
                        <td>Valor Total</td>
                        <td>Valor ICE</td>
                        <td>Acciones</td>
                    </tr>
                </tbody>
            </table>
        </div>
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
    tbody tr:last-child td:first-child {
        border-radius: 0%;
    } 
</style>
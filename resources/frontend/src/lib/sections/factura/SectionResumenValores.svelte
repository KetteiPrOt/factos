<script lang="ts">
    import type { ResumeInvoice } from "$lib/interfaces/invoice";
    import { onMount } from "svelte";
    import type { Writable } from "svelte/store";

    export let resumeInvoice: Writable<ResumeInvoice>;
    export let bodyTotals: Writable<boolean | undefined | null>;

    export let success: boolean;
    export let saving: boolean;

    $: {
        if (success) {
            Object.keys($resumeInvoice).forEach((key) => {
                console.log(key);
                $resumeInvoice[key as keyof typeof $resumeInvoice] = 0;
                console.log($resumeInvoice[key as keyof typeof $resumeInvoice]);
            });
            if (typeof window !== "undefined") {
                removeData();
            }
        }
    }

    $: {
        if (saving) {
            if (typeof window !== "undefined") {
                window.localStorage.setItem("tip_ten_percent", JSON.stringify($bodyTotals))            
            }
        }
    }

    function loadData () {
        const dataJson = window.localStorage.getItem("tip_ten_percent");
        if (dataJson !== null) {
            const data: boolean = JSON.parse(dataJson);
            bodyTotals.set(data);
        }
    }

    function removeData () {
        window.localStorage.removeItem("tip_ten_percent");
    }

    onMount(loadData);

</script>

<div class="flex flex-col border border-[--color-border] p-4 rounded-lg gap-4 box-border w-fit max-w-full place-self-center mb-auto">
    <section class="max-w-full">
        <div class="block w-full  rounded-md max-w-full overflow-x-auto">
            <table class="max-w-full w-full border-collapse text-center rounded-md">
                <thead class="bg-[--color-theme-1]">
                    <tr>
                        <th>Detalle</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Subtotal sin impuestos</td>
                        <td>{$resumeInvoice.r1.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>Subtotal 15%</td>
                        <td>{$resumeInvoice.r2.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>Subtotal 5%</td>
                        <td>{$resumeInvoice.r3.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>Subtotal tarifa especial</td>
                        <td>{$resumeInvoice.r4.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>Subtotal 0%</td>
                        <td>{$resumeInvoice.r5.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>Subtotal no objeto de iva</td>
                        <td>{$resumeInvoice.r6.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>Subtotal exento de iva</td>
                        <td>{$resumeInvoice.r7.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>Total descuento</td>
                        <td>{$resumeInvoice.r8.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>Valor ICE</td>
                        <td>{$resumeInvoice.r9.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>IVA 15%</td>
                        <td>{$resumeInvoice.r10.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>IVA 5%</td>
                        <td>{$resumeInvoice.r11.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>IVA tarifa especial</td>
                        <td>{$resumeInvoice.r12.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex flex-row gap-2 place-content-between">
                                <span class="">
                                    Propina 10%
                                </span>
                                <input bind:checked={$bodyTotals} name="tip_ten_percent" type="checkbox">
                            </div>
                        </td>
                        <td>{$resumeInvoice.r13.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>Valor a pagar</td>
                        <td>{$resumeInvoice.r14.toFixed(2)}</td>
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
        padding-inline: .5rem;
    }
    td:first-child {
        text-align: start;
    }
</style>
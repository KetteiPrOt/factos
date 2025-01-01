<script lang="ts">
    import InputPassword from "$lib/components/InputPassword.svelte";
    import type { Acquirer, AdditionalField, BodyRequestInvoice, Origin, PayMethod, ProductDetails, ResumeInvoice, Totals } from "$lib/interfaces/invoice";
    import type { IssuancePoint } from "$lib/interfaces/issuance_point";
    import type { PaginationIssuancePonints } from "$lib/interfaces/pagination";
    import type { Product } from "$lib/interfaces/product";
    import ModalAddAdditionalField from "$lib/sections/factura/ModalAddAdditionalField.svelte";
    import ModalAddPayMethod from "$lib/sections/factura/ModalAddPayMethod.svelte";
    import ModalEditAdditionalField from "$lib/sections/factura/ModalEditAdditionalField.svelte";
    import ModalEditPayMethod from "$lib/sections/factura/ModalEditPayMethod.svelte";
	import SectionAdquiriente from "$lib/sections/factura/SectionAdquiriente.svelte";
	import SectionBtns from "$lib/sections/factura/SectionBtns.svelte";
	import SectionCamposAdicionales from "$lib/sections/factura/SectionCamposAdicionales.svelte";
	import SectionDetalles from "$lib/sections/factura/SectionDetalles.svelte";
	import SectionFormasDePago from "$lib/sections/factura/SectionFormasDePago.svelte";
    import SectionOrigen from "$lib/sections/factura/SectionOrigen.svelte";
	import SectionResumenValores from "$lib/sections/factura/SectionResumenValores.svelte";
    import { onMount } from "svelte";
    import { writable, type Writable, type Readable, derived } from "svelte/store";
	import { fade } from "svelte/transition";

    
    // Section Origin
    let bodyOrigin: Writable<Origin> = writable({establishment_id: 0, issuance_date: "", issuance_point_id: 0});
    let targetEstab = writable(0);
    const issuancePoints: Writable<IssuancePoint[]> = writable([]);

    // Section Acquirer
    let bodyAcquirer: Writable<Acquirer> = writable({indentification: "", identification_type_id: 0, social_reason: "", email: ""});

    // Section Details
    let bodyDetails: Writable<ProductDetails[]> = writable([]);

    // Section Payment Methods
    let bodyPaymentMethods: Writable<PayMethod[]> = writable([]);

    const indexCurrentPayMethod: Writable<number> = writable(0);

    let modalAddPayMethodVisible = false;
    let modalEditPayMethodVisible = false;

    function toggleModalAddPayMethod () {
        modalAddPayMethodVisible = !modalAddPayMethodVisible;
    }

    function toggleModalEditPayMethod () {
        modalEditPayMethodVisible = !modalEditPayMethodVisible;
    }
    
    
    // Section Additional Fields
    let bodyAdditionalFields: Writable<AdditionalField[]> = writable([]);
    
    const indexCurrentAdditionalField: Writable<number> = writable(0);
        
    let modalAddAdditionalFieldVisible = false;
    let modalEditAdditionalFieldVisible = false;
    
    function toggleModalAddAdditionalField () {
        modalAddAdditionalFieldVisible = !modalAddAdditionalFieldVisible;
    }

    function toggleModalEditAdditionalField () {
        modalEditAdditionalFieldVisible = !modalEditAdditionalFieldVisible;
    }

    // Section Totals
    let bodyTotals: Writable<boolean> = writable(false);

    // Body Request
    let bodyRequest: Readable<BodyRequestInvoice> = derived([bodyOrigin, bodyAcquirer, bodyDetails, bodyPaymentMethods, bodyAdditionalFields, bodyTotals], ([$bodyOrigin]) => ({
        establishment_id: $bodyOrigin.establishment_id,
        issuance_date: $bodyOrigin.issuance_date,
        issuance_point_id: $bodyOrigin.issuance_point_id,
        identification: $bodyAcquirer.indentification,
        identification_type_id: $bodyAcquirer.identification_type_id,
        social_reason: $bodyAcquirer.social_reason,
        phone_number: $bodyAcquirer.phone_number,
        address: $bodyAcquirer.address,
        email: $bodyAcquirer.email,
        products: $bodyDetails,
        payment_methods: $bodyPaymentMethods,
        additional_fields: $bodyAdditionalFields,
        tip_ten_percent: $bodyTotals
    }))

    $: {
        console.log($bodyRequest)
    } 
    $: {
        console.log($bodyTotals)
    }

    
    // Update Functions
    const resumeInvoice: Writable<ResumeInvoice> = writable({
        r1: 0, r2: 0, r3: 0, r4: 0, r5: 0, r6: 0, r7: 0,
        r8: 0, r9: 0, r10: 0, r11: 0, r12: 0, r13: 0, r14: 0
    })
    function updateResumeInvoice () {
        Object.keys($resumeInvoice).forEach((k) => {
            $resumeInvoice[k as keyof typeof $resumeInvoice] = 0;
        })
        $bodyDetails.forEach((p)=>{
            $resumeInvoice.r1 += (p.price * p.amount) - p.discount;
            $resumeInvoice.r8 += p.discount;
        })
        $resumeInvoice.r2 = $resumeInvoice.r1;
        $resumeInvoice.r10 = $resumeInvoice.r2 * 0.15; 
        $resumeInvoice.r14 = $resumeInvoice.r1 + $resumeInvoice.r10;
        if ($bodyTotals) {
            $resumeInvoice.r13 = $resumeInvoice.r1 * 0.10;
            $resumeInvoice.r14 = $resumeInvoice.r1 + $resumeInvoice.r13;
        }
    }

    $: {
        $bodyTotals;
        if ($bodyDetails) {
            updateResumeInvoice();
        }
    }
        
    // Request Functions 
    async function loadIssuancePoints (url: string | undefined = '/api/issuance-points/'+$targetEstab+'?page=1') {
        if (!$targetEstab) {return}
        const res = await fetch(url, {
            headers: {
                'Accept': 'application/json'
            },
            credentials: 'include'
        });
        
        $issuancePoints = [];
        const data: PaginationIssuancePonints = await res.json();
        if (data.data) {
            $issuancePoints = data.data;
        }
    }

    const requestFunctions = {
        loadIssuancePoints: loadIssuancePoints
    }

    onMount(loadIssuancePoints);
	
</script>

<div in:fade class="flex flex-col p-5 gap-5 w-fit max-w-full h-full ">
    <h2 class="font-bold text-3xl text-center">Factura</h2>
    <div class="flex flex-col gap-7">
        <div class="flex flex-wrap gap-7 w-fit justify-center self-center">
            <SectionOrigen {bodyOrigin} {targetEstab} {requestFunctions} {issuancePoints}/>
            <SectionAdquiriente {bodyAcquirer} />
        </div>
        <SectionDetalles {bodyDetails} />
        <div class="flex flex-wrap gap-7 w-fit justify-center max-w-full self-center">
            <div class="flex flex-col gap-7 w-fit max-w-full">
                <SectionFormasDePago {bodyPaymentMethods} {indexCurrentPayMethod} {toggleModalAddPayMethod} {toggleModalEditPayMethod} />
                <SectionCamposAdicionales {bodyAdditionalFields} {indexCurrentAdditionalField} {toggleModalAddAdditionalField} {toggleModalEditAdditionalField} />
            </div>
            <SectionResumenValores {resumeInvoice} {bodyTotals} />
        </div>
        <SectionBtns />
    </div>
    <ModalAddPayMethod {bodyPaymentMethods} visible={modalAddPayMethodVisible} {toggleModalAddPayMethod} />
    <ModalEditPayMethod {bodyPaymentMethods} {indexCurrentPayMethod} visible={modalEditPayMethodVisible} {toggleModalEditPayMethod} />
    <ModalAddAdditionalField bodyAdditionalFields={bodyAdditionalFields} visible={modalAddAdditionalFieldVisible} {toggleModalAddAdditionalField} />
    <ModalEditAdditionalField {bodyAdditionalFields} {indexCurrentAdditionalField} visible={modalEditAdditionalFieldVisible} {toggleModalEditAdditionalField} />
</div>
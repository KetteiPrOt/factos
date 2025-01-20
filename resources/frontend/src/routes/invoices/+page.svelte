<script lang="ts">
    import { goto } from "$app/navigation";
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
	import { fade, slide } from "svelte/transition";

    
    // Section Origin
    let bodyOrigin: Writable<Origin> = writable({establishment_id: 0, issuance_date: "", issuance_point_id: 0});
    let targetEstab = writable(0);
    const issuancePoints: Writable<IssuancePoint[]> = writable([]);

    // Section Acquirer
    let bodyAcquirer: Writable<Acquirer> = writable({identification: "", identification_type_id: 0, social_reason: "", email: ""});

    // Section Details
    const bodyDetails: Writable<ProductDetails[]> = writable([]);

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
    let bodyAdditionalFields: Writable<AdditionalField[] | undefined> = writable([]);
    
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
    let bodyTotals: Writable<boolean | undefined | null> = writable(false);

    // Body Request
    let bodyRequest: Readable<BodyRequestInvoice> = derived([bodyOrigin, bodyAcquirer, bodyDetails, bodyPaymentMethods, bodyAdditionalFields, bodyTotals], ([$bodyOrigin]) => ({
        establishment_id: $bodyOrigin.establishment_id,
        issuance_date: $bodyOrigin.issuance_date,
        issuance_point_id: $bodyOrigin.issuance_point_id,
        identification: $bodyAcquirer.identification,
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

    // $: {
    //     console.log($bodyRequest)
    // } 
    // $: {
    //     console.log($bodyTotals)
    // }

    
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

    // Alerts Input
    const alertsInputOrigin = writable({
        establishment_id: false,
        issuance_date: false,
        issuance_point_id: false
    });

    const alertsInputAcquirer = writable({
        identification_type_id: false,
        identification: false,
        social_reason: false,
        phone_number: false,
        address: false,
        email: false,
    });

    let alertProducts = false;
    let alertPayMethods = false;

    $: {
        if ($bodyDetails.length > 0) {
            alertProducts = false;
        }
    }

    $: {
        if ($bodyPaymentMethods.length > 0) {
            alertPayMethods = false;
        }
    }

    function checkPhoneNumber () {
        let valid = true;
        if ($bodyAcquirer.phone_number) {
            const phoneLength = $bodyAcquirer.phone_number.length;
            console.log($alertsInputAcquirer.phone_number)
            if (phoneLength === 10) {
                Array.from($bodyAcquirer.phone_number).forEach((char) => {
                    if (isNaN(parseInt(char))) {
                        valid = false;
                    } 
                    console.log(isNaN(parseInt(char)))
                })
            } else {
                valid = false;
            }
        } else {
            valid = false;
        }

        return valid
    }

    const emailRegex = /^[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,}$/;
    
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

    let success = false;
    let failed = false;
    let failed422 = false;
    let saving = false;
    let loadingSendInvoice = false;
    async function sendInvoice () {
        if (loadingSendInvoice) { return };
        let valid = true;

        if (!$bodyOrigin.establishment_id) { $alertsInputOrigin.establishment_id = true ; valid = false };
        if (!$bodyOrigin.issuance_date) { $alertsInputOrigin.issuance_date = true ; valid = false };
        if (!$bodyOrigin.issuance_point_id) { $alertsInputOrigin.issuance_point_id = true ; valid = false};

        if (!$bodyAcquirer.identification_type_id) { $alertsInputAcquirer.identification_type_id = true ; valid = false}
        else if ($bodyAcquirer.identification_type_id !== 4) {
            if (!$bodyAcquirer.identification) { $alertsInputAcquirer.identification = true ; valid = false};
            if (!$bodyAcquirer.social_reason) { $alertsInputAcquirer.social_reason = true ; valid = false};
            if (!$bodyAcquirer.address) { $alertsInputAcquirer.address = true ; valid = false};
            if (!checkPhoneNumber()) { $alertsInputAcquirer.phone_number = true ; valid = false};
            if (!emailRegex.test($bodyAcquirer.email)) { $alertsInputAcquirer.email = true ; valid = false};
        } else {
            $alertsInputAcquirer.identification = false;
            $alertsInputAcquirer.social_reason = false;
            $alertsInputAcquirer.address = false;
            $alertsInputAcquirer.phone_number = false;
            $alertsInputAcquirer.email = false;
            $bodyAcquirer.identification = "";
            $bodyAcquirer.social_reason = "";
            $bodyAcquirer.email = "";
            $bodyAcquirer.address = "";
            $bodyAcquirer.phone_number = "";
        }
        
        if ($bodyDetails.length < 1) { alertProducts = true ; valid = false};
        
        if ($bodyPaymentMethods.length < 1) { alertPayMethods = true ; valid = false};

        if (!$bodyTotals) { $bodyTotals = undefined };
        if (Array.isArray($bodyAdditionalFields)) {
            if ($bodyAdditionalFields.length === 0) {$bodyAdditionalFields = undefined};
        };

        Object.values($alertsInputOrigin).forEach(value => {
            if (value) {
                valid = false;
            };
        });
        Object.values($alertsInputAcquirer).forEach(value => {
            if (value) {
                valid = false;
            };
        });
        Object.entries($alertsInputOrigin).forEach(([key, value]) => {
            console.log(key, ": ", value)
        });
        Object.entries($alertsInputAcquirer).forEach(([key, value]) => {
            console.log(key, ": ", value)
        });

        console.log("alertProducts: ", alertProducts);
        console.log("alertPayMethods: ", alertPayMethods);

        console.log("Valid: ", valid)

        if (!valid) { console.log("Invalid Invoice"); $bodyAdditionalFields = [] ; return }

        loadingSendInvoice = true;
        const res = await fetch('/api/receipts/invoices', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN']
            },
            method: 'POST',
            credentials: 'include',
            body: JSON.stringify($bodyRequest)
        })

        const data: {message: string} = await res.json();

        loadingSendInvoice = false;
        $bodyAdditionalFields = [];

        if (res.ok) {
            success = true;
            setTimeout(() => {
                success = false;
            }, 6000)
            clearInvoice();
            
        } else if (res.status === 422) {
            failed422 = true;
            setTimeout(() => {
                failed422 = false;
            }, 6000)
        } else if (data.message === 'Attempt to read property \"uploaded\" on null') {
            failed422 = true;
            setTimeout(() => {
                failed422 = false;
            }, 6000)
        } else {
            failed = true;
            setTimeout(() => {
                failed = false;
            }, 6000)
            
        }
    }

    function saveInvoice () {
        saving = true;
        setTimeout(() => {
            saving = false;
        }, 2000)
    }

    

    function clearInvoice () {
        $bodyOrigin.establishment_id = 0;
        $bodyOrigin.issuance_date = "";
        $bodyOrigin.issuance_point_id = 0;
    
        $bodyAcquirer.address = "";
        $bodyAcquirer.email = "";
        $bodyAcquirer.identification = "";
        $bodyAcquirer.identification_type_id = 0;
        $bodyAcquirer.phone_number = "";
        $bodyAcquirer.social_reason = "";
    
        $bodyDetails = [];
    
        $bodyPaymentMethods = [];
    
        $bodyAdditionalFields = [];
        
        $bodyTotals = false;
        // Object.keys($resumeInvoice).forEach((key) => {
        //     console.log(key);
        //     $resumeInvoice[key as keyof typeof $resumeInvoice] = 0;
        //     console.log($resumeInvoice[key as keyof typeof $resumeInvoice]);
        // })

    }

    const requestFunctions = {
        loadIssuancePoints: loadIssuancePoints
    }

    onMount(() => {
        loadIssuancePoints();
    });
	
</script>

<div in:fade class="flex flex-col p-5 gap-5 w-fit max-w-full h-full ">
    <h2 class="font-bold text-3xl text-center">Factura</h2>
    <div class="flex flex-col gap-7">
        <div class="flex flex-wrap gap-7 w-fit justify-center self-center">
            <SectionOrigen {success} {saving} {bodyOrigin} {targetEstab} {requestFunctions} {issuancePoints} {alertsInputOrigin} />
            <SectionAdquiriente {success} {saving} {bodyAcquirer} {alertsInputAcquirer} />
        </div>
        <SectionDetalles {bodyDetails} {success} {saving} />
        <div class="flex flex-wrap gap-7 w-fit justify-center max-w-full self-center">
            <div class="flex flex-col gap-7 w-fit max-w-full">
                <SectionFormasDePago {success} {saving} {bodyPaymentMethods} {indexCurrentPayMethod} {toggleModalAddPayMethod} {toggleModalEditPayMethod} />
                <SectionCamposAdicionales {success} {saving} {bodyAdditionalFields} {indexCurrentAdditionalField} {toggleModalAddAdditionalField} {toggleModalEditAdditionalField} />
            </div>
            <SectionResumenValores {saving} {success} {resumeInvoice} {bodyTotals} />
        </div>
    
        <div class="flex flex-col place-items-center">
            {#if success}
            <p transition:slide={{axis: "y"}} class="border border-lime-500 text-lime-600 rounded-md p-3">
                Factura emitida con éxito
            </p>                            
            {:else if failed422}
            <p transition:slide={{axis: "y"}} class="border border-red-500 text-red-600 rounded-md p-3">
                No se encontró la firma electrónica para procesar la factura
            </p>                            
            {:else if failed}
            <p transition:slide={{axis: "y"}} class="border border-red-500 text-red-600 rounded-md p-3">
                Error al procesar la factura
            </p>                            
            {:else if saving}
            <p transition:slide={{axis: "y"}} class="border border-yellow-500 text-yellow-600 rounded-md p-3">
                La factura ha sido guardada
            </p>                            
            {:else if Object.values($alertsInputOrigin).some(alert => alert)}
            <p transition:slide={{axis: "y"}} class="border border-red-500 text-red-500 rounded-md p-3">
                Errores encontrados en la sección Origen
            </p>            
            {:else if Object.values($alertsInputAcquirer).some(alert => alert)}
            <p transition:slide={{axis: "y"}} class="border border-red-500 text-red-500 rounded-md p-3">
                Errores encontrados en la sección Adquiriente
            </p>            
            {:else if alertProducts}
            <p transition:slide={{axis: "y"}} class="border border-red-500 text-red-500 rounded-md p-3">
                Necesita seleccionar un producto antes de continuar
            </p>
            {:else if alertPayMethods}
            <p transition:slide={{axis: "y"}} class="border border-red-500 text-red-500 rounded-md p-3">
                Necesita añadir un método de pago antes de continuar
            </p>
            {/if}
        </div>

        <SectionBtns {sendInvoice} {saveInvoice} {loadingSendInvoice} />
    </div>
    <ModalAddPayMethod {bodyPaymentMethods} visible={modalAddPayMethodVisible} {toggleModalAddPayMethod} />
    <ModalEditPayMethod {bodyPaymentMethods} {indexCurrentPayMethod} visible={modalEditPayMethodVisible} {toggleModalEditPayMethod} />
    <ModalAddAdditionalField {bodyAdditionalFields} visible={modalAddAdditionalFieldVisible} {toggleModalAddAdditionalField} />
    <ModalEditAdditionalField {bodyAdditionalFields} {indexCurrentAdditionalField} visible={modalEditAdditionalFieldVisible} {toggleModalEditAdditionalField} />
</div>
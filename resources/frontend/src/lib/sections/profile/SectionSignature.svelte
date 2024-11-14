<script lang="ts">
    import InputPassword from "$lib/components/InputPassword.svelte";
    import type { CertificateGet, CertificatePost } from "$lib/interfaces/certificate";
    import { onMount } from "svelte";
    import { writable, type Writable } from "svelte/store";
    import { scale, slide } from "svelte/transition";

    // Input Elements
    let inputCertificate: HTMLInputElement;
    let inputPassword: Writable<HTMLInputElement> = writable();

    // Alerts
    let alertsInput = {
        password: false,
        certificate: false
    }

    let alertFile = ""
    let msgError = ""

    let loading = false;

    // Certificate info
    let certificateGet: CertificateGet = {uploaded: false, effective_date: "", owner: ""}
    let certificatePost: CertificatePost = {password:""}


    // Uploadeda functions

    function updatePass (e: Event) {
        const input = e.target as HTMLInputElement;
        const value = input.value;
        
        certificatePost.password = value;
        alertsInput.password = false;
    }

    function updateCertificate (e: Event) {
        const input = e.target as HTMLInputElement;
        const files = input.files;

        if (!files || !files?.length) {
            if (certificatePost) {
                delete certificatePost.certificate;
            }
            return;
        }

        const file = files[0];

        // Validación de tamaño máximo
        const maxSize = 50 * 1024;
        if (file.size > maxSize) {
            alertFile = "Peso máximo de archivo 50KB"
            setTimeout(() => {
                alertFile = ""
            }, 2000)
            input.value = ""
            return;
        }

        certificatePost.certificate = file;
        alertsInput.certificate = false;
    }

    // Cookies
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

    // Request functions 
    async function getCertificate() {
        const res = await fetch('/api/certificate');
        const data: CertificateGet  = await res.json();
        //console.log(data);
        if (res.status === 200) {
            certificateGet = data;
            certificateGet.owner = data.owner.split('/')[1].replace('CN=', '')
        }
    }

    async function uploadCertificate () {
        // Validations 
        if (loading) {return};
        let valid = true;

        if (!certificatePost.certificate) { alertsInput.certificate = true ; valid = false }
        if (!certificatePost.password) { alertsInput.password = true ; valid = false }

        Object.values(alertsInput).forEach((alert) => {
            if (alert) {
                valid = false;
            }
        });

        if (!valid) { return };

        // FormData
        const formData = new FormData()
        formData.set('certificate', certificatePost.certificate as File);
        formData.set('password', certificatePost.password);
        if (inputCertificate) {
            inputCertificate.value = "";
        } 
        if ($inputPassword) {
            $inputPassword.value = "";
        }
        certificatePost.password = ""
        loading = true;
        const res = await fetch('/api/certificate?_method=PUT', {
            headers: {
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN']
            },
            method: 'POST',
            credentials: 'include',
            body: formData
        })

        loading = false;
        const data = await res.json();

        if (res.status === 200) {
            console.log(data);
            getCertificate();
        } else {
            msgError = data?.message as string;
            alertsInput.certificate = true;
            alertsInput.password = true;
            setTimeout(() => {
                msgError = "";
                alertsInput.certificate = false;
                alertsInput.password = false;
            }, 5000);
        }

    }

    onMount(getCertificate)
    
</script>

<div class="flex flex-col border border-[--color-border] p-4 rounded-lg gap-4 w-fit max-w-[405px] place-self-center">
    <section class="bg-[--color-theme-1] rounded-md">
        <h3 class="font-medium text-center text-slate-50 p-1.5">Firma</h3>
    </section>
    <section class="flex flex-col gap-2">
        <div class="flex flex-col gap-2">
            <div class="flex flex-row gap-1">
                <label for="cert">Certificado:</label>
                {#if alertFile}
                <span transition:scale class="text-red-500 font-light mx-auto">
                    {alertFile}
                </span>
                {/if}
            </div>
            <input bind:this={inputCertificate} name="cert" class="{alertsInput.certificate ? 'bg-red-500/70' : ''} rounded-md" type="file" accept=".p12" on:change={(e)=>updateCertificate(e)}>
        </div>
        <div class="flex flex-col gap-1">
            <label for="password">Contraseña:</label>
            <InputPassword bindInput={inputPassword} alert={alertsInput.password} name='' tailwindClass={'w-full'} updatePass={updatePass} />
        </div>
        <div class="flex flex-col gap-1">
            <label for="effective_date">Fecha vigencia:</label>
            <input bind:value={certificateGet.effective_date} name="effective_date" class="border border-[--color-border] bg-transparent rounded-md px-1" type="date" disabled>
        </div>
        <div class="flex flex-col gap-1">
            <label for="owner">Dueño:</label>
            <input bind:value={certificateGet.owner} name="owner" class="border border-[--color-border] bg-transparent rounded-md px-1" type="text" disabled>
        </div>
        <div class="flex flex-row text-slate-50 mt-4 gap-3">
            <button on:click={uploadCertificate} class="bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600">
                Cargar
            </button>
            {#if loading}
            <div transition:slide={{axis: "x"}} class="spinner" role="status" aria-live="polite" aria-label="Cargando">
            </div>
            {/if}
        </div>
        {#if msgError}
        <div transition:slide={{axis: "y"}} class="text-red-600 border border-red-500 text-center rounded-md">
            {msgError}
        </div>
        {/if}
    </section>
</div>

<style>
    .spinner {
        width: 25px;
        height: 25px;
        align-self: center;

        border: 5px solid white;
        border-top-color: blue;
        border-radius: 100%;

        animation: spin 1s infinite;
    }
    @keyframes spin {
        to {transform: rotate(360deg);}
    }
</style>
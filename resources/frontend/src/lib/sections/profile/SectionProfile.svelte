<script lang="ts">
    import { fly, scale, slide } from "svelte/transition";
    import ModalShowPreviewImg from "./ModalShowPreviewImg.svelte";
    import type { User } from "$lib/interfaces/user";
    import type { Writable } from "svelte/store";
    import { onMount } from "svelte";

    export let myUser: Writable<User>;
    export let getProfile: () => void;

    // Alerts
    let alertsInput = {
        name: false,
        email: false,
        ruc: false
    }

    let alertFile: string = "";

    let imgPreviewUrl = "";
    let imgActualUrl = "";
    let modalVisible = false;

    let loading = false;
    const emailRegex = /^[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,}$/;

    function toogleModalVisible () {
        modalVisible = !modalVisible;
    }

    function showPreview () {
        toogleModalVisible()
    }

    function setImgPreviewUrl (url: string, file: File) {
        imgPreviewUrl = url;
        $myUser.logoFile = file;
    }

    // HTML Elements
    let inputLogo: HTMLInputElement;

    $: {
        if ($myUser.logo) {
            imgActualUrl = $myUser.logo
        }
    }

    // Update alerts state
    $: { $myUser.name ; alertsInput.name = false }
    $: { $myUser.email ; alertsInput.email = false }
    $: { $myUser.ruc ; alertsInput.ruc = false }

    //Update logo validations
    function updateLogo (e: Event) {
        const target = e.target as HTMLInputElement;
        let file: File;
        if (target.files?.length) {
            file = target.files[0];
        } else {
            imgPreviewUrl = "";
            if ($myUser.logoFile) {
                delete $myUser.logoFile;
            }
            return;
        }
        // Validación de tamaño
        const maxSize = 512 * 1024;
        if (file.size > maxSize) {
            alertFile = "Peso máximo de archivo 512KB";
            setTimeout(() => {
                alertFile = "";
            }, 2000);
            target.value = "";
            return;
        }
        // Validación de aspecto
        const img = new Image();
        img.onload = () => {
            if (img.width !== img.height) {
                alertFile = "Proporción de imágen válida 1:1"
                setTimeout(() => {
                    alertFile = ""
                }, 2000);
                target.value = "";
                return;
            } else {
                setImgPreviewUrl(img.src, file);
            }
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            img.src = e.target?.result as string;
        }

        reader.readAsDataURL(file);
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

    // Validation functions
    function checkRUC () {
        let valid = true;
        if ($myUser.ruc) {
            const rucLength = $myUser.ruc.length;
            if (rucLength === 13) {
                Array.from($myUser.ruc).forEach((char) => {
                    if (isNaN(parseInt(char))) {
                        valid = false;
                    } 
                })
            } else {
                valid = false;
            }
        } else {
            valid = false;
        }

        return valid
    }
    
    //Request functions 
    async function sendUpdate () {
        // Validations 
        if (loading) { return };
        let valid = true;

        if (!$myUser.name) { alertsInput.name = true ; valid = false };
        if (!emailRegex.test($myUser.email)) { alertsInput.email = true ; valid = false };
        if (!checkRUC()) { alertsInput.ruc = true ; valid = false };

        Object.values(alertsInput).forEach(alert => {
            if (alert) {
                valid = false;
            }
        })

        if (!valid) { return }

        const formData = new FormData();

        formData.set('name', $myUser.name);
        formData.set('email', $myUser.email);
        formData.set('ruc', $myUser.ruc);
        if ($myUser.matrix_address) {
            formData.set('matrix_address', $myUser.matrix_address);
        }
        if ($myUser.logoFile) {
            formData.set('logo', $myUser.logoFile);
            if (inputLogo) {
                inputLogo.value = "";
            }
        }

        for (let [key, value] of formData.entries()) {
            console.log(key+": "+value);
        }

        loading = true;
        const res = await fetch('/api/profile?_method=PUT', {
            headers: {
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN']
            },
            method: 'POST',
            credentials: 'include',
            body: formData
        })
        
        loading = false;
        const data = await res.json();
        console.log(data);


        if (res.status === 200) {
            getProfile();
        }
    }

    onMount(getProfile)
    
</script>

<div class="flex flex-col border border-[--color-border] p-4 rounded-lg gap-4 w-fit max-w-[405px] place-self-center self-start">
    <section class="bg-[--color-theme-1] rounded-md">
        <h3 class="font-medium text-center text-slate-50 p-1.5">Perfil</h3>
    </section>
    <section class="flex flex-col gap-2">
        <div class="flex flex-col gap-2">
            <label for="name">Nombre:</label>
            <input bind:value={$myUser.name} name="name" class="border {alertsInput.name ? 'border-red-500' : 'border-[--color-border]'} bg-transparent rounded-md px-1 outline-none" type="text">
        </div>
        <div class="flex flex-col gap-1">
            <label for="email">Email:</label>
            <input bind:value={$myUser.email} name="email" class="border {alertsInput.email ? 'border-red-500' : 'border-[--color-border]'} bg-transparent rounded-md px-1 outline-none" type="text" autocomplete="off">
        </div>
        <div class="flex flex-col gap-1">
            <div class="flex flex-row gap-1">
                <label for="logo">Logo de emisor:</label>
                {#if alertFile}
                <span transition:scale class="text-red-500 font-light mx-auto">
                    {alertFile}
                </span>
                {/if}
            </div>
            <input bind:this={inputLogo} name="logo" class="outline-none" type="file" accept="image/*" on:change={(e)=>updateLogo(e)}>
        </div>
        <div>
            {#if imgPreviewUrl || imgActualUrl}
            <div transition:slide={{axis: 'y'}} class="border border-[--color-border] bg-transparent rounded-md overflow-hidden">
                <button class="rounded-md px-2 py-1 w-full" on:click={showPreview}>
                    Vista previa
                </button>
            </div>
            {/if}
        </div>
        <div class="flex flex-col gap-1">
            <label for="ruc">RUC:</label>
            <input bind:value={$myUser.ruc} name="ruc" class="border {alertsInput.ruc ? 'border-red-500' : 'border-[--color-border]'} bg-transparent rounded-md px-1 outline-none" type="text">
        </div>
        <div class="flex flex-col gap-1">
            <label for="matriz_address">Dirección matriz:</label>
            <input disabled bind:value={$myUser.matrix_address} name="matriz_address" class="border border-[--color-border] bg-transparent rounded-md px-1 outline-none" type="text" autocomplete="off">
        </div>
        <div class="flex flex-row text-slate-50 mt-4 gap-3">
            <button on:click={sendUpdate} class="bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600">
                Guardar
            </button>
            {#if loading}
            <div transition:slide={{axis: "x"}} class="spinner" role="status" aria-live="polite" aria-label="Cargando">
            </div>
            {/if}
        </div>
    </section>
    <ModalShowPreviewImg visible={modalVisible} toogleModalVisible={toogleModalVisible} {imgPreviewUrl} {imgActualUrl} />
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
<script lang="ts">
    import InputPassword from "$lib/components/InputPassword.svelte";
    import { writable, type Writable } from "svelte/store";
    import { slide } from "svelte/transition";

    let inputCurrentPassword: Writable<HTMLInputElement> = writable();
    let inputNewPassword: Writable<HTMLInputElement> = writable();
    let inputConfirmPassword: Writable<HTMLInputElement> = writable();

    let loading = false;

    // Password values
    let currentPassword = "";
    let newPassword = "";
    let confirmPassword = "";

    // Alerts input
    let alertsInput = {
        currentPassword: false,
        newPassword: false,
        confirmPassword: false
    }

    // Msg's Error
    let msgError = '';
    let msgInputError = '';

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

    // Update functions
    function updateCurrentPassword (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;

        currentPassword = value;
        alertsInput.currentPassword = false;
    }

    function updateNewPassword (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;

        newPassword = value;
        alertsInput.newPassword = false;
    }

    function updateConfirmPassword (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;

        confirmPassword = value;
        alertsInput.confirmPassword = false;
    }

    // Validation functions

    function validateNewPassword () {
        let valid = false;
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]).{1,}$/;
        if (newPassword.length >= 8 && newPassword.length <= 255 && regex.test(newPassword)) {
            valid = true;
        } else {
            msgInputError = 'Requerido: Mayúscula, Minúscula, Número y Símbolo | Longitud 8 - 255';
            setTimeout(() => {
                msgInputError = '';
            }, 2000)
        }

        return valid;
    }

    function validateConfirmPassword () {
        let valid = false;
        if (newPassword === confirmPassword && newPassword) {
            valid = true;
        }

        return valid;
    }   

    // Request functions
    async function sendUpdate () {
        if (loading) { return }
        let valid = true;
        if (!currentPassword) { alertsInput.currentPassword = true ; valid = false }
        if (!validateNewPassword()) { alertsInput.newPassword = true ; valid = false }
        if (!validateConfirmPassword()) { alertsInput.confirmPassword = true ; valid = false }

        Object.values(alertsInput).forEach((alert) => {
            if (alert) {
                valid = false;
            }
        })

        if (!valid) { return }

        loading = true;
        const res = await fetch('/api/profile/password', {
            headers: {
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN'],
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            method: 'PUT',
            credentials: 'include',
            body: JSON.stringify({
                current_password: currentPassword,
                new_password: newPassword,
                new_password_confirmation: confirmPassword
            })
        })

        loading = false;
        const data = await res.json();

        if (res.status === 200) {
            $inputCurrentPassword.value = "";
            $inputNewPassword.value = "";
            $inputConfirmPassword.value = "";
        } else {
            msgError = data?.message as string;
            alertsInput.currentPassword = true;
            setTimeout(() => {
                msgError = ''
            }, 6000)
        }

    }

</script>

<div class="flex flex-col border border-[--color-border] p-4 rounded-lg gap-4 w-full max-w-[405px] place-self-center">
    <section class="bg-[--color-theme-1] rounded-md">
        <h3 class="font-medium text-center text-slate-50 p-1.5">Cambiar contraseña</h3>
    </section>
    <section class="flex flex-col gap-2">
        <div class="flex flex-col gap-2">
            <label for="current_password">Contraseña actual:</label>
            <InputPassword alert={alertsInput.currentPassword} bindInput={inputCurrentPassword} updatePass={updateCurrentPassword} name="current_pasword" />
        </div>
        <div class="flex flex-col gap-1">
            <label for="new_password">Nueva contraseña:</label>
            <InputPassword alert={alertsInput.newPassword} bindInput={inputNewPassword} updatePass={updateNewPassword} name="new_pasword" />
            {#if msgInputError}
            <span transition:slide={{axis: "y", duration: 100}} class="text-red-500 font-extralight text-sm">
                {msgInputError}
            </span>
            {/if}
        </div>
        <div class="flex flex-col gap-1">
            <label for="confirm_password">Confirmar contraseña:</label>
            <InputPassword alert={alertsInput.confirmPassword} bindInput={inputConfirmPassword} updatePass={updateConfirmPassword} name="confirm_pasword" />
        </div>
        <div class="flex flex-row text-slate-50 mt-4 gap-3">
            <button class="bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={sendUpdate}>
                Guardar
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
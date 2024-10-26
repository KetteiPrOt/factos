<script lang="ts">
    import { goto } from "$app/navigation";
    import InputPassword from "$lib/components/InputPassword.svelte";
    import { writable } from "svelte/store";
    import { fade, scale, slide } from "svelte/transition";

    let inputEmailElement: HTMLInputElement;
    let inputPasswordElement: HTMLInputElement;
    const focusInputPass = writable(false);


    let credentials: {email: string, password: string, remember_me: boolean | undefined} = {
        email: "",
        password: "",
        remember_me: undefined
    }

    let alertsInput = {
        email: false,
        password: false
    }

    let loading = false;

    let resMessage = "";

    const emailRegex = /^[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,}$/;

    // Functions Credentials
    function updateEmail (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        credentials.email = value;
        alertsInput.email = false;
        if (resMessage) {
            resMessage = "";
            alertsInput.password = false;
        }
    }
    
    function updatePassword (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;
        credentials.password = value;
        alertsInput.password = false;
        if (resMessage) {
            resMessage = "";
            alertsInput.email = false;
        }
    }

    function updateRememberMe (e: Event) {
        const target = e.target as HTMLInputElement;
        const checked = target.checked;
        credentials.remember_me = checked;
    }

    function validateCredentials () {
        let valid = true;
        if (!credentials.email || !emailRegex.test(credentials.email)) { 
            alertsInput.email = true;
        };
        if (!credentials.password) { 
            alertsInput.password = true;
        };
        if (credentials.remember_me === false) {
            credentials.remember_me = undefined;
        }

        for (const alert of Object.values(alertsInput)) {
            if (alert) {
                valid = false;
            }
        }

        return valid
    }

    async function login () {
        if (!validateCredentials()) { return }

        loading = true;
        const resCSRF = await fetch('/sanctum/csrf-cookie', {headers: {'Accept': 'application/json'}, credentials: 'include'})
        const resLogin = await fetch('/api/login', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Xsrf-Token': getCookies()['XSRF-TOKEN'],
                'Referer': window.location.href,
                'Origin': window.location.origin
            },
            method: "POST",
            credentials: "include",
            body: JSON.stringify(credentials)
        });

        const data: {message: string} = await resLogin.json()

        loading = false;
        if (resLogin.status === 200) {
            goto('home');
        } else if (resLogin.status === 401) {
            alertsInput.email = true;
            alertsInput.password = true;
            resMessage = data.message;
        }
        console.log(credentials)
    }

    //Aditionals functions 
    function nextInput (e: KeyboardEvent) {
        if (e.key === "Enter") {
            e.preventDefault();
            let validEmail = true;
            if (!credentials.email || !emailRegex.test(credentials.email)) { 
                validEmail = false;
                alertsInput.email = true;
            };
            if (validEmail) {
                focusInputPass.set(true);
            } else {
                console.log(inputEmailElement)
                if (inputEmailElement) {
                    inputEmailElement.focus()
                }
            }

        }
    }

    function finalizedInputs (e: KeyboardEvent) {
        if (e.key === "Enter") {
            login()
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

</script>

<div in:fade>
    <div class="relative flex flex-col gap-10 h-fit w-fit max-w-fit mx-auto">
        <section>
            <h1 class="text-6xl text-[--color-theme-1] font-bold tracking-widest">
                FACTOS
            </h1>
        </section>
        <section class="flex flex-col gap-5 mt-10">
            <div class="flex flex-col gap-2">
                <label for="email" class="text-xl p-1">
                    Correo
                </label>
                <input bind:this={inputEmailElement} type="text" name="email" class="border {alertsInput.email ? 'border-red-500': 'border-[--color-border]'} bg-transparent rounded-md px-1 place-self-center w-full outline-none" on:input={(e)=>updateEmail(e)} on:keypress={(e)=>nextInput(e)}>
            </div>
            <div class="flex flex-col gap-2">
                <label for="password" class="text-xl p-1">
                    Contraseña
                </label>
                <!-- <input bind:this={inputPasswordElement} type="password" name="password" class="border {alertsInput.password ? 'border-red-500': 'border-[--color-border]'} bg-transparent rounded-md px-1 place-self-center w-full" on:input={(e)=>updatePassword(e)} on:keypress={(e)=>finalizedInputs(e)}> -->
                <InputPassword name="password" alert={alertsInput.password} updatePass={updatePassword} keyPress={finalizedInputs} focusInputPass={focusInputPass} />
            </div>
            <div class="flex flex-row gap-2 pl-1 items-center">
                <input type="checkbox" name="remember_me" class="border border-[--color-border] bg-transparent rounded-md overflow-hidden px-1 w-5 h-5" on:change={(e)=>updateRememberMe(e)}>
                <label for="remember_me" class="text-lg p-1">
                    Recuerdame
                </label>
            </div>
        </section>
        <section>
            <button class="h-[34px] bg-[--color-theme-1] py-1 px-2 rounded-md shadow-sm w-full text-slate-50 shadow-black hover:shadow hover:shadow-black hover:bg-blue-600" on:click={login}>
                {#if loading}
                <div in:scale class="spinner mx-auto" role="status" aria-live="polite" aria-label="Cargando">
                </div>
                {:else}
                <span in:scale>
                    Iniciar sesión
                </span>
                {/if}
            </button>
        </section>
    </div>
    {#if resMessage}
    <section transition:slide={{axis: "y"}} class="mt-10 flex justify-center">
        <span class="text-red-600">
            {resMessage}
        </span>
    </section>
    {/if}
</div>

<style>

    .spinner {
        width: 25px;
        height: 25px;
        /* min-width: 25px;
        min-height: 25px; */
        align-self: center;

        border: 5px solid white;
        border-top-color: var(--color-theme-1);
        border-radius: 100%;

        animation: spin 1s infinite;
    }
    @keyframes spin {
        to {transform: rotate(360deg);}
    }
</style>
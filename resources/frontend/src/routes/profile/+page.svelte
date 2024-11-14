<script lang="ts">
    import type { User } from "$lib/interfaces/user";


    import SectionChangePassword from "$lib/sections/profile/SectionChangePassword.svelte";
    import SectionProfile from "$lib/sections/profile/SectionProfile.svelte";
    import SectionSignature from "$lib/sections/profile/SectionSignature.svelte";
    import { onMount } from "svelte";
    import { writable, type Writable } from "svelte/store";
    import { fade } from "svelte/transition";

    // User info
    let myUser: Writable<User> = writable({name: '', email: '', ruc: '', logo: '', matrix_address: ''});

    // Request funcionts
    async function getProfile() {
        const res = await fetch('/api/profile', {
            headers: { 'Accept': 'application/json' }
        })
        
        const data: User = await res.json();
        $myUser = data;
    }

</script>

<div in:fade class="flex flex-col p-5 gap-5 w-fit max-w-full h-full ">
    <h2 class="font-bold text-3xl text-center">Información de Perfil</h2>
    <div class="flex flex-col gap-7">
        <div class="flex flex-wrap gap-7 w-fit justify-center self-center">
            <SectionProfile {myUser} {getProfile}/>
            <div class="flex flex-col gap-7 w-fit justify-center self-center">
                <SectionSignature />
                <SectionChangePassword />
            </div>
        </div>
        <div class="flex flex-wrap gap-7 w-fit justify-center max-w-full self-center">

        </div>
    </div>
</div>
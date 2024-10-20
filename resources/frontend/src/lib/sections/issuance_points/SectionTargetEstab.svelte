<script lang="ts">
    import Select from "$lib/components/Select.svelte";
    import type { Establishments } from "$lib/interfaces/establishments";
    import type { Option } from "$lib/interfaces/select";
    import { onMount } from "svelte";
    import type {Writable} from "svelte/store"

    export let targetEstab: Writable<number>;
    let estabs: Option[] = []
    let commercial_name_tracker: Option[] = []
    let commercial_name_to_show: string | undefined = ""

    async function getEstabs () {
        const res = await fetch("/api/establishments", {
            headers: {
                'Accept': 'application/json'
            },
            credentials: 'include'
        })

        const data: Establishments[] = await res.json();

        if (!data) { return }
        data.forEach((estab) => {
            let opt: Option = {id: estab.id, name: estab.code + " - " + estab.address}
            let commercial_name: Option = {id: estab.id, name: estab.commercial_name}
            estabs = [...estabs, opt]
            commercial_name_tracker = [...commercial_name_tracker, commercial_name]
        })

    };

    $: {
        commercial_name_to_show = "";
        if ($targetEstab && commercial_name_tracker) {
            commercial_name_to_show = commercial_name_tracker.find((cn) => {
                return cn.id === $targetEstab
            })?.name
        }
    }

    onMount(getEstabs)

</script>
<div class="flex flex-col gap-4 ">
    <div class="flex flex-row gap-5">
        <label for="establishment">
            Establecimiento:
        </label>
        <div class="mx-auto">
            <Select alert={false} optionSelected={targetEstab} options={estabs} pos="down" />
        </div>
    </div>
    <div class="flex flex-row gap-2">
        <label for="nombre">
            Nombre comercial:
        </label>
        <span class="mx-auto font-semibold">
            {commercial_name_to_show ? commercial_name_to_show : ''}
        </span>
        <!-- <input name="nombre" class="border border-[--color-border] bg-transparent rounded-md px-1 ml-auto" type="text" value={$nameToSearch} on:input={(e)=>updateNameToSearch(e)} on:keypress={(e)=>detectEnter(e)}> -->
    </div>

</div>
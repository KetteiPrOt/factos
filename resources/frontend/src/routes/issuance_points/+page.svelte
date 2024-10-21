<script lang="ts">
    import type { IssuancePoint, IssuancePointGet } from "$lib/interfaces/issuance_point";
    import type { PaginationIssuancePonints } from "$lib/interfaces/pagination";
    import ModalNewIssuancePoint from "$lib/sections/issuance_points/ModalNewIssuancePoint.svelte";
    import ModalViewIssuancePoint from "$lib/sections/issuance_points/ModalViewIssuancePoint.svelte";
    import SectionList from "$lib/sections/issuance_points/SectionList.svelte";
    import SectionOptions from "$lib/sections/issuance_points/SectionOptions.svelte";


    import SectionTargetEstab from "$lib/sections/issuance_points/SectionTargetEstab.svelte";
    import { onMount } from "svelte";
    import { writable, type Writable } from "svelte/store";
    import { fade } from "svelte/transition";

    let targetEstab = writable(0);
    let idToSearch = writable("");

    const paginationData: Writable<PaginationIssuancePonints> = writable();
    const issuancePoints: Writable<IssuancePoint[]> = writable([])
    const issuancePointSelected: Writable<IssuancePointGet> = writable({code: 0, description: "", active: false, sequentials: []});

    //Modals 
    let modalNewIssuancePointVisible = false;
    let modalViewIssuancePointVisible = false;

    function toogleModalNewIssuancePointVisible () {
        if ($targetEstab) {
            modalNewIssuancePointVisible = !modalNewIssuancePointVisible;
        }
    }

    function toogleModalViewIssuancePointVisible () {
        modalViewIssuancePointVisible = !modalViewIssuancePointVisible;
    }

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
            console.log(data)
            $issuancePoints = data.data;
        }
    }

    async function getIssuancePointById () {
        if (!$targetEstab) { return }
        const res = await fetch('/api/issuance-points/show/'+$idToSearch, {
            headers: {
                'Accept': 'application/json'
            },
            credentials: 'include'
        });

        if (res.status === 200) {
            let issPoint: IssuancePointGet = await res.json();
            console.log(issPoint);
            $issuancePointSelected = issPoint;
            if (typeof issPoint.code === 'string') {
                $issuancePointSelected.code = parseInt(issPoint.code)
            }
            // $issuancePointSelected.new = true;
        }
    }

    $: {
        if ($targetEstab != 0) {
            loadIssuancePoints();
        }
    }

    const requestFunctions = {
        loadIssuancePoints: loadIssuancePoints,
        getIssuancePointById: getIssuancePointById
    }

</script>

<div in:fade class="flex flex-col p-5 gap-5 w-fit h-full max-w-full">
	<h2 class="font-bold text-3xl text-center">Puntos de emisión</h2>
	<div class="flex flex-col gap-7">
        <SectionTargetEstab targetEstab={targetEstab} />
        <SectionOptions toogleModalNewEstabVisible={toogleModalNewIssuancePointVisible}  />
        <SectionList idToSearch={idToSearch} requestFunctions={requestFunctions} toogleModalViewIssuancePointVisible={toogleModalViewIssuancePointVisible} paginationData={paginationData} issuancePoints={issuancePoints} issuancePointSelected={issuancePointSelected} />
	</div>
    <ModalNewIssuancePoint requestFunctions={requestFunctions} targetEstab={targetEstab} toogleModalNewIssuancePointVisible={toogleModalNewIssuancePointVisible} visible={modalNewIssuancePointVisible} />
    <ModalViewIssuancePoint idToSearch={idToSearch} issuancePointSelected={issuancePointSelected} requestFunctions={requestFunctions} targetEstab={targetEstab} toogleModalViewIssuancePointVisible={toogleModalViewIssuancePointVisible} visible={modalViewIssuancePointVisible} />
</div>
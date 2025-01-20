<script lang="ts">
    import ResultsSearch from "$lib/components/ResultsSearch.svelte";
    import type { ProductDetails, ProductDetailsToShow, ProductGet, ProductSearch } from "$lib/interfaces/product";
    import { VatTypes } from "$lib/interfaces/vat";
    import { onMount } from "svelte";
	import { Icon } from "svelte-icons-pack";
	import { AiOutlineSearch } from "svelte-icons-pack/ai";
    import { BiTrash } from "svelte-icons-pack/bi";
    import { derived, writable, type Writable } from "svelte/store";
    import { fade } from "svelte/transition";

    export let bodyDetails: Writable<ProductDetails[]>;

    export let success: boolean;
    export let saving: boolean;

    let listProducts: ProductSearch[] = []
    let selectedProduct = writable(0);

    let listProductsDetails: ProductDetailsToShow[] = [];

    // Input Elements
    let inputLook: HTMLInputElement;

    // Update Functions
    function updateLook (e: Event) {
        const target = e.target as HTMLInputElement;
        const value = target.value;

        listProducts = []
        if (value) {
            searchProducts(value);
        }
    }

    function deleteProductDetails (e: Event) {
        const target = e.currentTarget as HTMLButtonElement;
        const parentTR = target.parentElement?.parentElement;
        const idProduct = parseInt(parentTR?.dataset.id as string);

        let newListProductsDetails: ProductDetailsToShow[] = listProductsDetails.filter((p) => {
            if (p.id !== idProduct) {
                return true
            } else {
                return false
            }
        })

        listProductsDetails = [...newListProductsDetails];
        
    }

    //REQUEST FUNCTIONS
	async function searchProducts (look: string) {
        const res = await fetch(`/api/products/search?look=${look}`,
            {
                headers: {'Accept': 'application/json'}
            }
        );

        const data: ProductSearch[] = await res.json();
        //console.log(data);
        listProducts = []
        if (res.status === 200 && (inputLook ? (inputLook.value ? true : false) : false)) {
            data.forEach((product) => {
                let index = data.indexOf(product);
                listProducts = [...listProducts, product]
                // setTimeout(() => {

                // }, index * 10)
            })
            //listProducts = data;
        }
    };

    async function getProductById () {
        if (!$selectedProduct) { return }

        const res = await fetch('/api/products/'+$selectedProduct, {
            credentials: "include",
            headers: {
                'Accept': 'application/json'
            }
        })

        if (res.status === 200) {
            const product: ProductGet = await res.json()
            //console.log(product);
            let newProductDetails: ProductDetailsToShow = {
                id: product.id as number,
                code: product.code,
                name: product.name,
                price: parseFloat(product.price.toString()),
                vat_rate: VatTypes.find((vat)=> { if (vat.id === product.vat_rate_id) {return true} })?.name as string,
                amount: 1,
                discount: 0.00,
                totalValue: product.price
            };

            if (!listProductsDetails.find((p)=>{if(p.id===newProductDetails.id){ p.amount++ ;return true}})) {
                listProductsDetails = [...listProductsDetails, newProductDetails];
            } else {
                listProductsDetails = [...listProductsDetails];
            }

            //console.log(listProductsDetails);
        }

    }

    $: {
        if ($selectedProduct) {
            getProductById();
            listProducts = []
            if (inputLook) {
                inputLook.value = ""
            }
            selectedProduct.set(0);
        }
        
    } 

    
    $: {
        bodyDetails.set([])
        listProductsDetails.forEach((p) => {
            //p.discount = parseFloat(p.discount.toFixed(2))
            p.totalValue = (p.price * p.amount) - p.discount;
            //Set Section BodyDetails
            $bodyDetails = [...$bodyDetails, {product_id: p.id, amount: p.amount, price: p.price, discount: p.discount}]
        });
    }
    
    $: {
        if (success) {
            listProductsDetails = [];
            if (typeof window !== "undefined") {
                removeData();
            }
        };
    }

    $: {
        if (saving) {
            if (typeof window !== "undefined") {
                window.localStorage.setItem("list_products_details", JSON.stringify(listProductsDetails))            
            }
        }
    }

    function loadData () {
        const dataJson = window.localStorage.getItem("list_products_details");
        if (dataJson !== null) {
            const data: ProductDetailsToShow[] = JSON.parse(dataJson);
            listProductsDetails = data;
        }
    }

    function removeData () {
        window.localStorage.removeItem("list_products_details");
    }

    onMount(loadData);


</script>
<div class="flex flex-col border border-[--color-border] p-4 rounded-lg gap-4 box-border w-full max-w-full place-self-center">
    <section class="bg-[--color-theme-1] rounded-md">
        <h3 class="font-medium text-center text-slate-50 p-1.5">Detalles</h3>
    </section>
    <section class="flex flex-row gap-3 place-items-center">
        <span>Código / Descripción</span>
        <div class="flex flex-row relative place-items-center border border-[--color-border] rounded-md">
            <input bind:this={inputLook} name="identificacion" spellcheck="false" class="outline-none bg-transparent px-1" type="text" placeholder="Search" on:input={e=>updateLook(e)}>
            <button class="p-1 hover:bg-[--color-theme-1] hover:text-slate-50">
                <Icon src={AiOutlineSearch} size={20} />
            </button>
            <ResultsSearch options={listProducts} optionSelected={selectedProduct} />
        </div>
    </section>
    <section class="max-w-full">
        <div class="block w-full  rounded-md max-w-full overflow-x-auto">
            <table class="max-w-full w-full border-collapse text-center">
                <thead class="bg-[--color-theme-1]">
                    <tr>
                        <th>Código</th>
                        <th>Cantidad</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Tarifa</th>
                        <th>Descuento</th>
                        <th>Valor Total</th>
                        <!-- <th>Valor ICE</th> -->
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {#each listProductsDetails as productDetails}
                    <tr in:fade={{delay: (listProductsDetails.indexOf(productDetails)*50)}} class="hover:bg-slate-200" data-id="{productDetails.id}">
                        <td>
                            <button class="w-full h-full p-2">
                                {productDetails.code}
                            </button>
                        </td>
                        <td>
                            <button class="w-full h-full py-2">
                                <input bind:value={productDetails.amount} class="outline-none bg-transparent border border-[--color-border] rounded-sm w-[50px] pl-1" min="1" type="number">
                            </button>
                        </td>
                        <td>
                            <button class="w-full h-full p-2">
                                {productDetails.name}
                            </button>
                        </td>
                        <td>
                            <button class="w-full h-full p-2">
                                {productDetails.price ? productDetails.price.toFixed(2) : 0}
                            </button>
                        </td>
                        <td>
                            <button class="w-full h-full p-2">
                                {productDetails.vat_rate}
                            </button>
                        </td>
                        <td>
                            <button class="w-full h-full py-2">
                                <input bind:value={productDetails.discount} min="0" step="0.01" class="outline-none bg-transparent border border-[--color-border] rounded-sm w-[60px] pl-1" type="number">
                            </button>
                        </td>
                        <td>
                            <button class="w-full h-full p-2">
                                {productDetails.totalValue ? productDetails.totalValue.toFixed(2) : ''}
                            </button>
                        </td>
                        <!-- <td>
                            <button class="w-full h-full p-2">
                                Valor ICE
                            </button>
                        </td> -->
                        <td>
                            <button class="flex w-full h-full place-content-center place-items-center p-2 text-[--color-theme-1] hover:text-blue-500" on:click={e=>deleteProductDetails(e)}>
                                <Icon src={BiTrash} size={22} />
                            </button>
                        </td>
                    </tr>       
                    {/each}
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
        padding: .5rem;
    }
    tbody tr:last-child td:first-child {
        border-radius: 0%;
    } 
</style>
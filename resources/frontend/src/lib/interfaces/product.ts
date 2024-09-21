export  interface Product {
    id: number
    code: string
    name: string
    price: string
    vat_rate: string
}

export interface ProductPost extends Omit<Product, 'id' | 'vat_rate' | 'price'> {
    price: number
    additional_info?: string
    tourism_vat_applies?: boolean
    ice_applies?: boolean
    ice_type_id?: number
    vat_rate_id: number
}
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

export interface ProductGet extends ProductPost {
   id?: number
   new?: boolean
}

export interface ProductSearch extends Omit<Product, 'price' | 'vat_rate'> {

}

export interface ProductDetailsToShow extends Omit<Product, 'price'> {
    price: number
    amount: number
    discount: number
    totalValue: number
}

export interface ProductDetails {
    product_id: number
    amount: number
    price: number
    discount: number
}
export interface Origin {
    establishment_id: number
    issuance_date: string
    issuance_point_id: number
}

export interface Acquirer {
    indentification: string
    identification_type_id: number
    social_reason: string
    phone_number?: string | null
    address?: string | null
    email: string
}

export interface ProductDetails {
    product_id: number
    amount: number
    price: number
    discount: number
}

export interface PayMethod {
    pay_method_id: number
    value: number
    term?: number
    time?: string
}

export interface AdditionalField {
    name: string
    description: string
}

export interface Totals {
    tip_ten_percent?: number
}


export interface BodyRequestInvoice {
    //Section Origin
    establishment_id: number
    issuance_date: string
    issuance_point_id: number
    //Section Acquirer
    identification: string
    identification_type_id: number
    social_reason: string
    phone_number?: string | null
    address?: string | null
    email: string
    //Section Details
    products: ProductDetails[]
    //Section Payment Methods
    payment_methods: PayMethod[]
    //Section Additional Fields
    additional_fields?: AdditionalField[]
    //Section Totals
    tip_ten_percent?: number
}
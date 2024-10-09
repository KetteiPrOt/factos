export interface Establishments {
    id: number
    code: string
    address: string
    commercial_name: string
}

export interface EstablishmentsPost extends Omit<Establishments, 'id' | 'code'> {
    code: number
}

export interface EstablishmentsGet extends EstablishmentsPost {
    new?: boolean
}

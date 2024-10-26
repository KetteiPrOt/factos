export interface Person {
    id: number
    identification: string
    social_reason: string
    email: string
    identification_type: string
}

export interface PersonPost extends Omit<Person, 'id' | 'identification_type'> {
    phone_number?: string
    address?: string
    identification_type_id: number
}

export interface PersonGet extends Omit<Person, 'identification_type'> {
    phone_number?: string | null
    address?: string | null
    created_at: string
    updated_at: string
    identification_type_id: number
    user_id: number
    new?: boolean
}
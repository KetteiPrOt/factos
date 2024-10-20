export interface Sequential {
    next: number
    receipt_type_id: number
}

export interface IssuancePoint {
    id: number
    code: string
    description?: string
    active: boolean
    sequentials: Sequential[]
}

export interface IssuancePointPost extends Omit<IssuancePoint, 'id' | 'code' | 'sequentials' | 'active'> {
    code: number
    sequentials: {
        "1": number,
        "2": number,
        "3": number,
        "4": number,
        "5": number,
        "6": number,
    }
}

export interface IssuancePointGet extends Omit<IssuancePoint, 'id' | 'code' | 'sequentials' > {
    code: number
    sequentials: Sequential[] | object
    // new?: boolean
}
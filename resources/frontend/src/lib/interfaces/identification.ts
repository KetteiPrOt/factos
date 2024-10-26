export interface Identification {
    id: number
    name: string
}

export const IdentificationTypes : Identification[] = [
    {
        id: 1,
        name: "RUC"
    },
    {
        id: 2,
        name: "Cédula"
    },
    {
        id: 3,
        name: "Pasaporte"
    },
    {
        id: 4,
        name: "Venta a consumidor final"
    },
    {
        id: 5,
        name: "Identificación del exterior"
    }
]

export const IdentificationTypesExcludeFinalConsumer : Identification[] = [
    {
        id: 1,
        name: "RUC"
    },
    {
        id: 2,
        name: "Cédula"
    },
    {
        id: 3,
        name: "Pasaporte"
    },
    {
        id: 5,
        name: "Identificación del exterior"
    }
]
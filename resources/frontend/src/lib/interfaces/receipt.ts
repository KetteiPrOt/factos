export interface Receipt {
    id: number
    name: string
}

export const ReceiptTypes : Receipt[] = [
    {
        id: 1,
        name: "Factura"
    },
    {
        id: 2,
        name: "Liquidación de compra de bienes y prestación de servicios"
    },
    {
        id: 3,
        name: "Nota de credito"
    },
    {
        id: 4,
        name: "Nota de débito"
    },
    {
        id: 5,
        name: "Guía de remisión"
    },
    {
        id: 6,
        name: "Comprobante de retención"
    }
]
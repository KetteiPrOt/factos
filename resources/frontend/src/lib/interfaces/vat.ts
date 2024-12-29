export interface Vat {
    id: number;
    name: string;
    percentaje: number;
}

export const VatTypes: Vat[] = [
    {
        id: 1,
        name: "0%",
        percentaje: 0,
    },
    { 
        id: 2,
        name: "15%", 
        percentaje: 15 
    },
    { 
        id: 3, 
        name: "5%", 
        percentaje: 5 
    },
    { 
        id: 4, 
        name: "No Objeto de Impuesto", 
        percentaje: 0 
    },
    { 
        id: 5, 
        name: "Exento de IVA",
        percentaje: 0 
    }
];

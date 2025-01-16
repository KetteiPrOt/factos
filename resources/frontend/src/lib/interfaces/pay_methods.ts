export interface PayMethodType {
    id: number;
    code: string;
    name: string;
}

export const PayMethods: PayMethodType[] = [
    { id: 1, code: "01", name: "SIN UTILIZACIÓN DEL SISTEMA FINANCIERO" },
    { id: 2, code: "15", name: "COMPENSACIÓN DE DEUDAS" },
    { id: 3, code: "16", name: "TARJETA DE DÉBITO" },
    { id: 4, code: "17", name: "DINERO ELECTRÓNICO" },
    { id: 5, code: "18", name: "TARJETA PREPAGO" },
    { id: 6, code: "19", name: "TARJETA DE CRÉDITO" },
    { id: 7, code: "20", name: "OTROS CON UTILIZACIÓN DEL SISTEMA FINANCIERO" },
    { id: 8, code: "21", name: "ENDOSO DE TÍTULOS" },
];

export interface TimeType {
    id: number
    name: string
}

export const Times: TimeType[] = [
    { id: 1, name: "Años" },
    { id: 2, name: "Meses" },
    { id: 3, name: "Días" }
]
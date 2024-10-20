import type { IssuancePoint } from "./issuance_point";
import type { Product } from "./product";

export interface Pagination {
    data: Product[]
    links: {
        first: string,
        last: string,
        prev?: string,
        next?: string
    }
    meta: {
        current_page: number,
        last_page: number,
        total: number
    }
}

export interface PaginationIssuancePonints extends Omit<Pagination, 'data'> {
    data: IssuancePoint[]
}
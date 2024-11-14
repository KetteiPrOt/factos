export interface CertificateGet {
    uploaded: boolean
    effective_date?: string | null
    owner: string
}

export interface CertificatePost {
    certificate?: File
    password: string
}
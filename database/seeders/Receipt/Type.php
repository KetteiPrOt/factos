<?php

namespace Database\Seeders\Receipt;

use Illuminate\Database\Seeder;
use App\Models\Receipts\Type as RecieptType;

class Type extends Seeder
{
    private array $types = [
        ['FACTURA', '01'],
        ['LIQUIDACIÓN DE COMPRA DE BIENES Y PRESTACIÓN DE SERVICIOS', '03'],
        ['NOTA DE CRÉDITO', '04'],
        ['NOTA DE DÉBITO', '05'],
        ['GUÍA DE REMISIÓN', '06'],
        ['COMPROBANTE DE RETENCIÓN', '07']
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach($this->types as $type){
            RecieptType::create([
                'name' => $type[0],
                'code' => $type[1]
            ]);
        }
    }
}

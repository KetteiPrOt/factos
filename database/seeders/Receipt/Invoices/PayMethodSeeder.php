<?php

namespace Database\Seeders\Receipt\Invoices;

use App\Models\Receipts\Invoices\PayMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PayMethodSeeder extends Seeder
{
    private array $types = [
        ['SIN UTILIZACIÓN DEL SISTEMA FINANCIERO', '01'],
        ['COMPENSACIÓN DE DEUDAS', '15'],
        ['TARJETA DE DÉBITO', '16'],
        ['DINERO ELECTRÓNICO', '17'],
        ['TARJETA PREPAGO', '18'],
        ['TARJETA DE CRÉDITO', '19'],
        ['OTROS CON UTILIZACIÓN DEL SISTEMA FINANCIERO', '20'],
        ['ENDOSO DE TÍTULOS', '21']
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach($this->types as $type){
            PayMethod::create([
                'name' => $type[0],
                'code' => $type[1]
            ]);
        }
    }
}

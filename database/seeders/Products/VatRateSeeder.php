<?php

namespace Database\Seeders\Products;

use App\Models\Products\VatRate;
use Illuminate\Database\Seeder;

class VatRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach($this->vatRates() as $vatRate){
            VatRate::create([
                'code' => $vatRate[0],
                'percentaje' => $vatRate[1],
                'name' => $vatRate[2],
            ]);
        }
    }

    // Ficha_Tecnica => pagina 24 => tabla 17
    private function vatRates(): array
    {
        // code (for XML), percentaje, name (Frontend label)
        return [
            ['0', 0, '0%'],
            ['4', 15, '15%'],
            ['5', 5, '5%'],
            ['6', 0, 'No Objeto de Impuesto'],
            ['7', 0, 'Exento de IVA'],
        ];
    }
}

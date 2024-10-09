<?php

namespace Database\Seeders\Persons;

use App\Models\Persons\IdentificationType;
use Illuminate\Database\Seeder;

class IdentificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach($this->identificationTypes() as $identificationType){
            IdentificationType::create([
                'code' => $identificationType[0],
                'name' => $identificationType[1]
            ]);
        }
    }

    // Ficha_Tecnica => pagina 12 => tabla 6
    private function identificationTypes(): array
    {
        // code (for XML), name (Frontend label)
        return [
            ['04', 'RUC'],
            ['05', 'CÉDULA'],
            ['06', 'PASAPORTE'],
            ['07', 'VENTA A CONSUMIDOR FINAL'],
            ['08', 'IDENTIFICACIÓN DEL EXTERIOR'],
        ];
    }
}

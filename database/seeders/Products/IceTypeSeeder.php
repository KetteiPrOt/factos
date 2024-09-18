<?php

namespace Database\Seeders\Products;

use App\Models\Products\IceType;
use Illuminate\Database\Seeder;

class IceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach($this->iceTypes() as $iceType){
            IceType::create([
                'code' => $iceType[0],
                'name' => $iceType[1]
            ]);
        }
    }

    // Ficha_Tecnica => pagina 24 => tabla 18
    private function iceTypes(): array
    {
        // code (for XML), name (Frontend label)
        return [
            ['3000', 'ICE NO ESPECIFICADO'],
            ['3011', 'ICE Cigarrillos Rubios - 0,17 - 0,16'],
            ['3021', 'ICE Cigarrillos Negros - 0,17 - 0,16'],
            ['3023', 'ICE Productos del Tabaco y Sucedáneos del Tabaco excepto Cigarrillos 150% - 150% -'],
            ['3031', 'ICE Bebidas Alcohólicas 75% 10,36 75% 10,00'],
            ['3041', 'ICE Cerveza Industrial Gran Escala 75% - 75% -'],
            ['3041', 'ICE Cerveza Industrial Mediana Escala 75% - 75% -'],
            ['3041', 'ICE Cerveza Industrial Pequeña Escala 75% - 75% -'],
            ['3073', 'ICE Vehículos Motorizados cuyo PVP sea hasta de 20000 USD 5% - 5% -'],
            ['3075', 'ICE Vehículos Motorizados PVP entre 30000 y 40000 15% - 15% -'],
            ['3077', 'ICE Vehículos Motorizados cuyo PVP superior USD 40.000 hasta 50.000 20% - 20% -'],
            ['3078', 'ICE Vehículos Motorizados cuyo PVP superior USD 50.000 hasta 60.000 25% - 25% -'],
            ['3079', 'ICE Vehículos Motorizados cuyo PVP superior USD 60.000 hasta 70.000 30% - 30% -'],
            ['3080', 'ICE Vehículos Motorizados cuyo PVP superior USD 70.000 35% - 35% -'],
            ['3081', 'ICE Aviones, Tricares, yates, Barcos de Recreo 15% - 10% -'],
            ['3092', 'ICE Servicios de Televisión Prepagada 0% - 0% -'],
            ['3610', 'ICE Perfumes y Aguas de Tocador 20% - 20% -'],
            ['3620', 'ICE Videojuegos 0% - 0% -'],
            ['3630', 'ICE Armas de Fuego, Armas deportivas y Municiones 300% - 30% -'],
            ['3640', 'ICE Focos Incandescentes 100% - 100%'],
            ['3660', 'ICE Cuotas Membresías Afiliaciones Acciones 35% - 35% -'],
            ['3093', 'ICE Servicios Telefonía Sociedades 15% - 15% -'],
            ['3101', 'ICE Bebidas Energizantes 10% - 10% -'],
            ['3053', 'ICE Bebidas Gaseosas con Alto Contenido de Azúcar - 0,19 por 100 gramos de azúcar - 0,18 por 100 gramos de azúcar'],
            ['3054', 'ICE Bebidas Gaseosas con Bajo Contenido de Azúcar 10% - 10% -'],
            ['3111', 'ICE Bebidas No Alcohólicas - 0,19 por 100 gramos de azúcar - 0,18 por 100 gramos de azúcar'],
            ['3043', 'ICE Cerveza Artesanal - 1,55 - 1,50'],
            ['3033', 'ICE Alcohol 75% 10,36 75% 10,00'],
            ['3671', 'ICE calefones y sistemas de calentamiento de agua a gas SRI 100% - 100% - '],
            ['3684', 'ICE vehículos motorizados camionetas y de rescate cuyo PVP sea hasta DE 30.000 USD 5% - 5% - '],
            ['3686', 'ICE vehículos motorizados excepto camionetas y de rescate cuyo PVP sea superior USD 20.000 hasta DE 30.000 10% - 10% -'],
            ['3688', 'ICE vehículos híbridos cuyo PVP sea de hasta USD. 35.000 0% 0%'],
            ['3691', 'ICE vehículos híbridos cuyo PVP superior USD. 35.000 hasta 40.000 8% 8%'],
            ['3692', 'ICE vehículos híbridos cuyo PVP superior USD. 40.000 hasta 50.000 14% 14%'],
            ['3695', 'ICE vehículos híbridos cuyo PVP superior USD. 50.000 hasta 60.000 20% 20%'],
            ['3696', 'ICE vehículos híbridos cuyo PVP superior USD. 60.000 hasta 70.000 26% 26%'],
            ['3698', 'ICE vehículos híbridos cuyo PVP superior a USD 70.000 32% - 32% -'],
            ['3682', 'ICE consumibles tabaco calentado y líquidos con nicotina SRI 150% - 150% -'],
            ['3681', 'ICE servicios de telefonía móvil personas naturales 0% - 0% -'],
            ['3680', 'ICE fundas plásticas - 0,10 - 0,08'],
            ['3533', 'ICE Import. Bebidas Alcohólicas 75% - 75% -'],
            ['3541', 'ICE Cerveza Gran Escala CAE 75% - 75% -'],
            ['3541', 'ICE Cerveza Industrial de Mediana Escala CAE 75% - 75% -'],
            ['3541', 'ICE Cerveza Industrial de Pequeña Escala CAE 75% - 75% -'],
            ['3542', 'ICE Cigarrillos Rubios CAE - - - -'],
            ['3543', 'ICE Cigarrillos Negros CAE - - - -'],
            ['3544', 'ICE Productos del Tabaco y Sucedáneos del Tabaco Excepto Cigarrillos CAE 150% - 150% -'],
            ['3581', 'ICE Aeronaves CAE 15% - 10% -'],
            ['3582', 'ICE Aviones, Avionetas y Helicópteros Exct. Aquellos destinados Al Trans. CAE 15% - 10% -'],
            ['3710', 'ICE Perfumes Aguas de Tocador Cae 20% - 20% -'],
            ['3720', 'ICE Video Juegos CAE 35% - 35% -'],
            ['3730', 'ICE Importaciones Armas de Fuego, Armas deportivas y Municiones CAE 300% - 30% -'],
            ['3740', 'ICE Focos Incandescentes CAE 100% - 100% -'],
            ['3871', 'ICE-vehículos motorizados cuyo PVP SEA hasta de 20000 USD SENAE 5% - 5% -'],
            ['3873', 'ICE-vehículos motorizados PVP entre 30000 Y 40000 SENAE 15% - 15% -'],
            ['3874', 'ICE-vehículos motorizados cuyo PVP superior USD 40.000 hasta 50.000 SENAE 20% - 20% -'],
            ['3875', 'ICE-vehículos motorizados cuyo PVP superior USD 50.000 hasta 60.000 SENAE 25% - 25% -'],
            ['3876', 'ICE-vehículos motorizados cuyo PVP superior USD 60.000 hasta 70.000 SENAE 30% - 30% -'],
            ['3877', 'ICE-vehículos motorizados cuyo PVP superior USD 70.000 SENAE 35% - 35% -'],
            ['3878', 'ICE-Aviones, Tricares, Yates, Barcos de Rec SENAE 15% - 10% -'],
            ['3601', 'ICE Bebidas Energizantes SENAE 10% - 10% -'],
            ['3552', 'ICE bebidas gaseosas con alto contenido de azúcar SENAE - 0,19 por 100 gramos de azúcar - 0,18 por 100 gramos de azúcar'],
            ['3553', 'ICE bebidas gaseosas con bajo contenido de azúcar SENAE 10% - 10% -'],
            ['3602', 'ICE bebidas no alcohólicas SENAE - 0,19 por 100 gramos de azúcar - 0,18 por 100 gramos de azúcar'],
            ['3545', 'ICE cerveza artesanal SENAE 75% 1,55 75% 1,5'],
            ['3532', 'ICE Import. alcohol SENAE 75% 10,36 75% 10'],
            ['3671', 'ICE calefones y sistemas de calentamiento de agua a gas SRI 100% - 100% -'],
            ['3771', 'ICE calefones y sistemas de calentamiento de agua a gas SENAE 100% - 100% -'],
            ['3685', 'ICE vehículos motorizados camionetas y de rescate PVP sea hasta DE 30.000 USD SENAE 5% - 5% -'],
            ['3687', 'ICE vehículos motorizados excepto camionetas y de rescate cuyo PVP sea superior USD 20.000 hasta de 30.000 SENAE 10% - 10% -'],
            ['3689', 'ICE vehículos híbridos cuyo PVP sea de hasta USD. 35.000 SENAE 0% - 0% -'],
            ['3690', 'ICE vehículos híbridos cuyo PVP superior USD. 35.000 hasta 40.000 SENAE 8% - 8% -'],
            ['3693', 'ICE vehículos híbridos cuyo PVP superior USD. 40.000 hasta 50.000 SENAE 14% - 14% -'],
            ['3694', 'ICE vehículos híbridos cuyo PVP superior USD. 50.000 hasta 60.000 SENAE 20% - 20% -'],
            ['3697', 'ICE vehículos híbridos cuyo PVP superior USD. 60.000 hasta 70.000 SENAE 26% - 26% -'],
            ['3699', 'ICE vehículos híbridos cuyo PVP superior a USD 70.000 SENAE 32% - 32% -'],
            ['3683', 'ICE consumibles tabaco calentado y líquidos con nicotina SENAE 150% 50%'],
        ];
    }
}

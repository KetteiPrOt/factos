<?php

namespace Database\Seeders\Establishments;

use App\Models\User;
use Illuminate\Database\Seeder as BaseSeeder;
use App\Models\Establishments\Model as Establishment;
use Illuminate\Database\Eloquent\Factories\Sequence;
use App\Models\Establishments\IssuancePoint;
use App\Models\Establishments\Sequential;
use App\Models\Receipts\Type as RecieptType;

class Seeder extends BaseSeeder
{
    /**
     * Count of establishments that will be created for each user
     */
    private int $establishments_per_user = 3;

    /**
     * Count of issuance points that will be created for each establishment
     */
    private int $issuance_points_per_establishment = 3;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        foreach($users as $user){
            Establishment::factory()
                ->count($this->establishments_per_user)
                ->has(
                    IssuancePoint::factory()
                        ->count($this->issuance_points_per_establishment)
                        ->state(new Sequence(
                            ...$this->issuancePointSequence($this->issuance_points_per_establishment)
                        ))
                    ,'issuancePoints'
                )->state(new Sequence(
                    ...$this->establishmentSequence(
                        $this->establishments_per_user, $user->id
                    )
                ))->create();
        }
        $reciept_types_count = RecieptType::all()->count();
        foreach(Establishment::all() as $establishment){
            foreach($establishment->issuancePoints as $issuancePoint){
                Sequential::factory()
                    ->count($reciept_types_count)
                    ->state(new Sequence(
                        ...$this->sequentialSequence($issuancePoint->id)
                    ))
                    ->create();
            }
        }
    }

    private function sequentialSequence(int $issuance_point_id): array
    {
        $sequence = [];
        foreach(RecieptType::all() as $recieptType){
            $sequence[] = [
                'issuance_point_id' => $issuance_point_id,
                'receipt_type_id' => $recieptType->id
            ];
        }
        return $sequence;
    }

    private function issuancePointSequence(int $count): array
    {
        $sequence = [];
        for($i = 1; $i <= $count; $i++){
            $sequence[] = ['code' => $this->numberToChar($i)];
        }
        return $sequence;
    }

    private function establishmentSequence(int $count, int $user_id): array
    {
        $sequence = [];
        for($i = 1; $i <= $count; $i++){
            $sequence[] = [
                'code' => $this->numberToChar($i),
                'user_id' => $user_id
            ];
        }
        return $sequence;
    }

    private function numberToChar(int $number): string
    {
        if($number < 100 && $number >= 10){
            $number = "0$number";
        } else if($number < 10 && $number >= 1){
            $number = "00$number";
        }
        return $number;
    }
}

<?php

namespace App\Http\Controllers\Establishments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Establishments\IssuancePoint\StoreRequest;
use App\Models\Establishments\Model as Establishment;
use App\Http\Resources\Establishments\IssuancePoints\Index\PaginatedCollection;
use App\Models\Establishments\IssuancePoint;
use App\Models\Establishments\Sequential;
use App\Models\Receipts\Type as ReceiptType;

class IssuancePointController extends Controller
{
    public function index(Establishment $establishment)
    {
        $this->authUser()->checkModelBelongsToMe($establishment, relationship: 'establishments');
        return new PaginatedCollection(
            $establishment->issuancePoints()->with('sequentials')->paginate(15)
        );
    }

    public function store(StoreRequest $request, Establishment $establishment)
    {
        $validated = $request->validated();
        $validated['code'] = $this->numberToChar($validated['code']);
        $validated['establishment_id'] = $establishment->id;
        $issuancePoint = IssuancePoint::create($validated);
        $this->storeSequentials($issuancePoint, $validated['sequentials']);
        return response(['message' => 'Guardado.'], 200);
    }

    private function storeSequentials(IssuancePoint $issuancePoint, array $data): void
    {
        $receiptTypes = ReceiptType::all();
        foreach($receiptTypes as $receiptType){
            Sequential::create([
                'next' => $data[$receiptType->id],
                'issuance_point_id' => $issuancePoint->id,
                'receipt_type_id' => $receiptType->id
            ]);
        }
    }
}

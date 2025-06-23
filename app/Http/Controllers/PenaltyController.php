<?php

namespace App\Http\Controllers;

use App\Models\TransactionPenalty;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    public function fetchData()
    {
        $penalties = TransactionPenalty::with([
            'user',
            'item',
            'item.category',
            'transaction.item',
            'transaction.user',
            'transaction.item.category',
            'transaction.item.category.laboratory' // if nested like this
        ])->get();

        $data = $penalties->map(function ($penalty, $index) {
            return [
                'count' => $index + 1,
                'borrower_name' => $penalty->user->first_name . ' ' . $penalty->user->last_name,
                'item_name' => $penalty->item->item_name,
                'quantity' => $penalty->quantity,
                'status' => $penalty->status,
                'remarks' => $penalty->remarks,
                'laboratory' => optional($penalty->item->category->laboratory)->name ?? 'N/A',
            ];
        });

        return response()->json($data);
    }
}

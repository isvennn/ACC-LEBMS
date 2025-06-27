<?php

namespace App\Http\Controllers;

use App\Models\TransactionPenalty;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    public function fetchData()
        {
            $user = auth()->user();

            $penaltiesQuery = TransactionPenalty::with([
                'user',
                'item.category.laboratory',
                'transaction.user'
            ]);

            if (in_array($user->user_role, ['Laboratory Head', 'Laboratory In-charge'])) {
                // Show only penalties for items that belong to their laboratory
                $penaltiesQuery->whereHas('item.category.laboratory', function ($query) use ($user) {
                    $query->where('id', $user->laboratory_id);
                });
            } elseif ($user->user_role === 'Employee') {
                // Show penalties for transactions made by this employee
                $penaltiesQuery->whereHas('transaction', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            }

            $penalties = $penaltiesQuery->get();

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

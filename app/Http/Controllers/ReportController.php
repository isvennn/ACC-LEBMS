<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionPenalty;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockSummaryExport;
use App\Exports\TransactionHistoryExport;
use App\Exports\PenaltySummaryExport;
use App\Exports\OverdueTransactionsExport;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function stockSummary(Request $request): JsonResponse|BinaryFileResponse
    {
        $query = Item::with(['category.laboratory'])
            ->select('items.item_name', 'items.item_description', 'items.current_qty', 'items.beginning_qty', 'items.item_price', 'categories.category_name', 'laboratories.name as laboratory_name')
            ->join('categories', 'items.category_id', '=', 'categories.id')
            ->join('laboratories', 'categories.laboratory_id', '=', 'laboratories.id');

        if ($request->laboratory_id) {
            $query->where('laboratories.id', $request->laboratory_id);
        }

        if ($request->category_type) {
            $query->where('categories.category_type', $request->category_type);
        }

        $data = $query->get()->groupBy('laboratory_name');

        if ($request->export === 'excel') {
            return Excel::download(new StockSummaryExport($data), 'stock_summary_' . now()->format('Ymd_His') . '.xlsx');
        }

        return response()->json([
            'data' => $data,
            'filters' => [
                'laboratory_id' => $request->laboratory_id,
                'category_type' => $request->category_type,
            ],
        ]);
    }

    public function transactionHistory(Request $request): JsonResponse|BinaryFileResponse
    {
        $query = Transaction::with(['item', 'user'])
            ->select('transactions.transaction_no', 'transactions.reserve_quantity', 'transactions.approve_quantity', 'transactions.date_of_usage', 'transactions.date_of_return', 'transactions.time_of_return', 'transactions.status', 'items.item_name', 'users.first_name', 'users.last_name')
            ->join('items', 'transactions.item_id', '=', 'items.id')
            ->join('users', 'transactions.user_id', '=', 'users.id');

        if ($request->laboratory_id) {
            $query->join('categories', 'items.category_id', '=', 'categories.id')
                ->where('categories.laboratory_id', $request->laboratory_id);
        }

        if ($request->user_id) {
            $query->where('transactions.user_id', $request->user_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('transactions.date_of_usage', [$request->start_date, $request->end_date]);
        }

        if ($request->status) {
            $query->where('transactions.status', $request->status);
        }

        $data = $query->get();

        if ($request->export === 'excel') {
            return Excel::download(new TransactionHistoryExport($data), 'transaction_history_' . now()->format('Ymd_His') . '.xlsx');
        }

        return response()->json([
            'data' => $data,
            'filters' => [
                'laboratory_id' => $request->laboratory_id,
                'user_id' => $request->user_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
            ],
        ]);
    }

    public function penaltySummary(Request $request): JsonResponse|BinaryFileResponse
    {
        $query = TransactionPenalty::with(['transaction', 'item', 'user'])
            ->select('transaction_penalties.quantity', 'transaction_penalties.amount', 'transaction_penalties.status', 'transaction_penalties.remarks', 'transactions.transaction_no', 'items.item_name', 'users.first_name', 'users.last_name')
            ->join('transactions', 'transaction_penalties.transaction_id', '=', 'transactions.id')
            ->join('items', 'transaction_penalties.item_id', '=', 'items.id')
            ->join('users', 'transaction_penalties.user_id', '=', 'users.id');

        if ($request->laboratory_id) {
            $query->join('categories', 'items.category_id', '=', 'categories.id')
                ->where('categories.laboratory_id', $request->laboratory_id);
        }

        if ($request->user_id) {
            $query->where('transaction_penalties.user_id', $request->user_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('transaction_penalties.created_at', [$request->start_date, $request->end_date]);
        }

        $data = $query->get();

        if ($request->export === 'excel') {
            return Excel::download(new PenaltySummaryExport($data), 'penalty_summary_' . now()->format('Ymd_His') . '.xlsx');
        }

        return response()->json([
            'data' => $data,
            'filters' => [
                'laboratory_id' => $request->laboratory_id,
                'user_id' => $request->user_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ],
        ]);
    }

    public function overdueTransactions(Request $request): JsonResponse|BinaryFileResponse
    {
        $query = Transaction::with(['item', 'user'])
            ->select('transactions.transaction_no', 'transactions.reserve_quantity', 'transactions.date_of_return', 'transactions.status', 'items.item_name', 'users.first_name', 'users.last_name')
            ->join('items', 'transactions.item_id', '=', 'items.id')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->where('transactions.status', '!=', 'Returned')
            ->where('transactions.status', '!=', 'Cancelled')
            ->where('transactions.date_of_return', '<', now());

        if ($request->laboratory_id) {
            $query->join('categories', 'items.category_id', '=', 'categories.id')
                ->where('categories.laboratory_id', $request->laboratory_id);
        }

        if ($request->user_id) {
            $query->where('transactions.user_id', $request->user_id);
        }

        $data = $query->get();

        if ($request->export === 'excel') {
            return Excel::download(new OverdueTransactionsExport($data), 'overdue_transactions_' . now()->format('Ymd_His') . '.xlsx');
        }

        return response()->json([
            'data' => $data,
            'filters' => [
                'laboratory_id' => $request->laboratory_id,
                'user_id' => $request->user_id,
            ],
        ]);
    }
}

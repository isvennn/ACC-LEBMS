<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionHistoryExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($item) {
            return [
                'Transaction No' => $item->transaction_no,
                'Item' => $item->item_name,
                'User' => $item->user ? $item->user->full_name : 'N/A',
                'Reserve Quantity' => $item->reserve_quantity,
                'Approve Quantity' => $item->approve_quantity,
                'Date of Usage' => $item->date_of_usage,
                'Date of Return' => $item->date_of_return,
                'Time of Return' => $item->time_of_return,
                'Status' => ucfirst($item->status),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Transaction No',
            'Item',
            'User',
            'Reserve Quantity',
            'Approve Quantity',
            'Date of Usage',
            'Date of Return',
            'Time of Return',
            'Status',
        ];
    }
}

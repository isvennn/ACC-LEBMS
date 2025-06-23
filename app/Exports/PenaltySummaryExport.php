<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PenaltySummaryExport implements FromCollection, WithHeadings
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
                'Quantity' => $item->quantity,
                'Amount' => number_format($item->amount, 2),
                'Status' => ucfirst($item->status),
                'Remarks' => ucfirst($item->remarks),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Transaction No',
            'Item',
            'User',
            'Quantity',
            'Amount',
            'Status',
            'Remarks',
        ];
    }
}

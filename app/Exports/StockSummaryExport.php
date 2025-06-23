<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockSummaryExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = [];
        foreach ($this->data as $laboratory => $items) {
            foreach ($items as $item) {
                $rows[] = [
                    'Laboratory' => $laboratory,
                    'Category' => $item->category_name,
                    'Item Name' => $item->item_name,
                    'Description' => $item->item_description,
                    'Beginning Quantity' => $item->beginning_qty,
                    'Current Quantity' => $item->current_qty,
                    'Price' => $item->item_price,
                ];
            }
        }
        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Laboratory',
            'Category',
            'Item Name',
            'Description',
            'Beginning Quantity',
            'Current Quantity',
            'Price',
        ];
    }
}

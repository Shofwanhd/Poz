<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class TransaksiExport implements FromCollection, WithHeadings, WithColumnFormatting

{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return ['idTransaksi', 'Customer', 'Discount', 'Subtotal', 'Total', 'DiscountAmount', 'Paid', 'Change', 'Cashier', 'Date'];
    }

    public function collection()
    {
        return collect($this->data)->map(function ($item) {
            return [
                'idTransaksi' => $item->idTransaksi,
                'Customer' => $item->namaPelanggan,
                'Discount' => $item->discount_name,
                'Subtotal' => $item->subtotal,
                'Total' => $item->total,
                'DiscountAmount' => $item->discount,
                'Paid' => $item->paid_amount,
                'Change' => $item->total,
                'Cashier' => $item->cashier,
                'Date' => $item->created_at,
            ];
        });
    }

    public function columnFormats(): array
    {
        return [
            'D' => '"Rp" #,##0',
            'E' => '"Rp" #,##0',
            'F' => '"Rp" #,##0',
            'G' => '"Rp" #,##0',
            'H' => '"Rp" #,##0',
        ];
    }
}

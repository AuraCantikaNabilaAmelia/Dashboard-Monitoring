<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BidwasExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Bidang/Unit',
            'Kode',
            'Jumlah Pegawai',
            'Total ST',
            'Total LHP',
            'Penyelesaian (%)',
        ];
    }

    public function map($item): array
    {
        static $index = 0;
        $index++;
        
        $rate = $item->total_st > 0 ? round(($item->total_lhp / $item->total_st) * 100) : 0;
        
        return [
            $index,
            $item->nama_bidwas,
            $item->kode,
            $item->total_pegawai,
            $item->total_st,
            $item->total_lhp,
            $rate . '%',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

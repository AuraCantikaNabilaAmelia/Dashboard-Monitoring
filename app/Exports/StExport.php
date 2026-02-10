<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
            'ID ST',
            'Nomor ST',
            'Nama Penugasan',
            'Bidang',
            'Mulai',
            'Selesai',
            'Status',
        ];
    }

    public function map($st): array
    {
        return [
            $st->id_st,
            $st->no_st,
            $st->nama,
            $st->nm_bidwas ?? '-',
            \Carbon\Carbon::parse($st->start_date)->format('d-m-Y'),
            \Carbon\Carbon::parse($st->end_date)->format('d-m-Y'),
            $st->status ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

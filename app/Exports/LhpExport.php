<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LhpExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
            'ID LHP',
            'Nomor LHP',
            'Nomor ST',
            'Nama Penugasan',
            'Bidang',
            'Tanggal LHP',
            'Status',
        ];
    }

    public function map($lhp): array
    {
        return [
            $lhp->id_lhp,
            $lhp->no_lhp,
            $lhp->no_st,
            $lhp->nama,
            $lhp->nm_bidwas ?? '-',
            \Carbon\Carbon::parse($lhp->tgl_lhp)->format('d-m-Y'),
            $lhp->status ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

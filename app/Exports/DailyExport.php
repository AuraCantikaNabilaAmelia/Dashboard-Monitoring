<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class DailyExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
            'Nama Pegawai',
            'NIP',
            'Nama Penugasan',
            'Mulai',
            'Selesai',
            'Peran',
        ];
    }

    public function map($item): array
    {
        static $index = 0;
        $index++;
        
        return [
            $index,
            $item->nama,
            $item->nip,
            $item->st_nama,
            Carbon::parse($item->start_date)->format('d-m-Y'),
            Carbon::parse($item->end_date)->format('d-m-Y'),
            $item->peran,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

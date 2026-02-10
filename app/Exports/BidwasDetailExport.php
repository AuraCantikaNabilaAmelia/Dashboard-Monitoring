<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BidwasDetailExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $collectionData;
    private $currentRowIndex = 0;

    public function __construct($collectionData)
    {
        $this->collectionData = $collectionData;
    }

    public function collection()
    {
        return $this->collectionData;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'NIP',
            'Jabatan',
            'ST Aktif',
            'Total ST'
        ];
    }

    public function map($employeeRow): array
    {
        $roleDisplayNames = [
            'staff' => 'Staff',
            'korwas_apd_1' => 'Korwas APD 1',
            'korwas_apd_2' => 'Korwas APD 2',
            'korwas_an_1' => 'Korwas AN 1',
            'korwas_an_2' => 'Korwas AN 2',
            'korwas_ipp_1' => 'Korwas IPP 1',
            'korwas_ipp_2' => 'Korwas IPP 2',
            'korwas_investigasi_1' => 'Korwas Investigasi 1',
            'korwas_investigasi_2' => 'Korwas Investigasi 2',
            'korwas_p3a' => 'Korwas P3A',
            'kepala_perwakilan' => 'Kepala Perwakilan',
            'kepala_bagian_umum' => 'Kepala Bagian Umum',
            'subkoor_keuangan' => 'Subkoordinator Keuangan',
            'subkoor_bmn_rt_kearsipan' => 'Subkoordinator BMN & RT',
        ];

        return [
            ++$this->currentRowIndex,
            $employeeRow->nama,
            $employeeRow->nip,
            $roleDisplayNames[$employeeRow->user_role] ?? $employeeRow->user_role,
            $employeeRow->active_tasks,
            $employeeRow->total_assignments
        ];
    }

    public function styles(Worksheet $worksheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

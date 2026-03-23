<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DriverExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $drivers;

    public function __construct($drivers)
    {
        $this->drivers = $drivers;
    }

    public function collection()
    {
        return $this->drivers;
    }

    public function headings(): array
    {
        return [
            'Employee ID',
            'Name',
            'Phone',
            'Email',
            'License Number',
            'License Expiry',
            'License Status',
            'Joining Date',
            'Status',
            'Assigned Vehicles',
        ];
    }

    public function map($driver): array
    {
        return [
            $driver->employee_id,
            $driver->full_name,
            $driver->phone,
            $driver->email ?? '-',
            $driver->license_number,
            $driver->license_expiry ? $driver->license_expiry->format('d-m-Y') : '',
            $driver->getLicenseStatusLabel(),
            $driver->joining_date ? $driver->joining_date->format('d-m-Y') : '',
            $driver->is_active ? 'Active' : 'Inactive',
            $driver->vehicles->pluck('vehicle_no')->implode(', '),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
            ],
        ];
    }
}

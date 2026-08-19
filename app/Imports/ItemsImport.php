<?php

namespace App\Imports;

use App\Models\Item;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class ItemsImport implements ToCollection, WithHeadingRow
{
    public function __construct()
    {
        HeadingRowFormatter::default('none');
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $row = $row->toArray();

            $sku = trim((string) ($row['SKU'] ?? ''));

            if ($sku === '') {
                continue;
            }

            $name = trim((string) ($row['제품명'] ?? ''));
            $unit = trim((string) ($row['UNIT'] ?? ''));

            if ($name === '') {
                continue;
            }

            if ($unit === '') {
                continue;
            }

            Item::updateOrCreate(
                [
                    'sku' => $sku,
                ],
                [
                    'barcode' => $this->nullIfEmpty(
                        $row['바코드'] ?? null
                    ),

                    'name' => $name,

                    'unit' => $unit,

                    'brand' => $this->nullIfEmpty(
                        $row['BRAND'] ?? null
                    ),

                    'color' => $this->nullIfEmpty(
                        $row['COLOR'] ?? null
                    ),

                    'size' => $this->nullIfEmpty(
                        $row['SIZE'] ?? null
                    ),

                    'is_active' => true,
                ]
            );
        }
    }

    private function nullIfEmpty(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
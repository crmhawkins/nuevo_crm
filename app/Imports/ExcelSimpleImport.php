<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExcelSimpleImport implements ToCollection, WithHeadingRow
{
    public $data;

    public function collection(Collection $rows) {
        $this->data = $rows;
    }
}

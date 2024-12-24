<?php

namespace App\Imports;

use App\Models\Partner;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;

class PartnerImport implements ToModel
{
    use Importable;

    public function model(array $row)
    {
        if (empty($row[0])) {
            return null;
        }

        return new Partner([
            'name' => $row[0],
            'address' => $row[1],
        ]);
    }
}

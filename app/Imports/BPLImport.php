<?php

namespace App\Imports;

use App\Models\BPL;
use Maatwebsite\Excel\Concerns\ToModel;

class BPLImport implements ToModel
{
    public function model(array $row)
    {
        if (empty($row[0])) {
            return null;
        }

        return new BPL([
            'item_name' => $row[0],
            'unit' => $row[1],
        ]);
    }
}

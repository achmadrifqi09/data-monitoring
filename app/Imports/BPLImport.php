<?php

namespace App\Imports;

use App\Models\BPL;
use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToModel;

class BPLImport implements ToModel
{
    public function model(array $row)
    {
        if (empty($row[0]) || empty($row[1])) {
            return null;
        }

        $bpl = BPL::where('bpl_number', $row[0])->first();
        if (!$bpl) {
            BPL::create([
                'bpl_number' => $row[0],
            ]);
        }

        return new Item([
            'bpl_number' => $row[0],
            'item_name' => $row[1],
            'unit' => $row[2],
        ]);
    }
}

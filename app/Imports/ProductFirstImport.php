<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductFirstImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
        $nrow = 0;
        foreach($collection as $row) {
            $ncol = 0;
            foreach($row as $cell) {
                $cell;
                $ncol ++;
            }
            $nrow ++;
        }
    }
}

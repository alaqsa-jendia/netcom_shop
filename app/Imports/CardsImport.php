<?php

namespace App\Imports;

use App\Models\Card;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CardsImport implements ToModel, WithHeadingRow
{
    protected $packageId;

    public function __construct($packageId)
    {
        $this->packageId = $packageId;
    }

    public function model(array $row)
    {
        return new Card([
            'username' => $row['username'],
            'password' => $row['password'],
            'package_id' => $this->packageId,
            'status' => 'available',
        ]);
    }
}

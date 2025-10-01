<?php

namespace App\Imports;

use App\Models\Products\ProductModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class ProductsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

        public function model(array $row)
    {
       return new ProductModel([
            'user_id'        => $row['user_id'],             // logged-in user
            'category_id'    => $row['category_id'],    // from Excel
            'title'          => $row['title'],
            'image'          => $row['image'] ?? null,  // optional column
            'price'          => $row['price'],
            'discount_price' => $row['discount_price'],
            'affiliate_url'  => $row['affiliate_url'],
            'amazon'         => $row['amazon'],
            'e_bay'          => $row['e_bay'],
            'etsy'           => $row['etsy'],
            'walmart'        => $row['walmart'],
            'description'    => $row['description'],
            'status'         => 1,                      // default
        ]);
    }
    }


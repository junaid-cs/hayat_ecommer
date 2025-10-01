<?php

namespace App\Models\Wishlist;

use App\Models\Products\ProductModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishlistModel extends Model
{
     protected $table = 'wishlist';
    protected $primaryKey = 'id';

    // product relation
        public function product(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }
}

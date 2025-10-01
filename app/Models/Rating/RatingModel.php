<?php

namespace App\Models\Rating;
use App\Models\Products\ProductModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class RatingModel extends Model
{
       protected $table = 'rating';
    protected $primaryKey = 'id';

    // product relation
        public function product(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }

    // user relation
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }   
}

<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Category\CategoryModel;
use App\Models\User;


class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'image',
        'price',
        'discount_price',
        'affiliate_url',
        'amazon',
        'e_bay',
        'etsy',
        'walmart',
        'description',
        'status',
    ];
    // author
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    // category
    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    // ratings relation
    public function ratings()
    {
        return $this->hasMany('App\Models\Rating\RatingModel', 'product_id', 'id');
    }
}

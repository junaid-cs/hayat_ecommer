<?php
namespace App\Models\Category;

use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    protected $table    = 'category';
    protected $fillable = [
        'name',
        'image',
        'status',
    ];

}

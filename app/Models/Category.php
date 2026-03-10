<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenant;

class Category extends Model
{
    use HasFactory, HasTenant;
    protected $fillable = ['tenant_id', 'name', 'unit_name', 'metric_type', 'description', 'image_path'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

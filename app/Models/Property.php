<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'broker_id', 'address', 'listing_type', 'city', 'zip_code', 'discription', 'build_year'
    ];

    public function characteristic()
    {
        return $this->hasOne(PropertyCharacteristic::class);    
    }
}

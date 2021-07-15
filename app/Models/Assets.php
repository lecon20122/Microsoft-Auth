<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assets extends Model
{
    use HasFactory;

    protected $guarded = [];
    // protected $fillable = ['name' , 'asset_type' , 'resource' , 'ancestors' , 'update_time'];
    protected $casts = [
        'ancestors' => 'array', // Will convarted to (Array)
        'resource' => 'array', // Will convarted to (Array)
        'ancestors' => 'array', // Will convarted to (Array)
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachments extends Model
{
    protected $table = 'attachments';

    protected $fillable = [
        'measurement_id',
        'url'
    ];

    public function measurement()
    {
        return $this->belongsTo(Measurement::class);
    }
}

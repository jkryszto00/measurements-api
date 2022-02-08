<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachments extends Model
{
    protected $table = 'attachments';

    public function measurement()
    {
        return $this->belongsTo(Measurement::class);
    }
}

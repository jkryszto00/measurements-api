<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Measurement extends Model
{
    use SoftDeletes;

    protected $table = 'measurements';
    protected $fillable = [
        'date',
        'unit',
        'netto',
        'brutto',
        'product',
        'plate',
        'customer',
        'driver',
        'modified_by_id',
        'notes'
    ];

    public function attachments()
    {
        return $this->hasMany(Attachments::class);
    }

    public function modifiedBy()
    {
        return $this->belongsTo(User::class);
    }
}

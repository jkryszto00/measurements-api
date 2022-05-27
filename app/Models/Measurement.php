<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Measurement extends Model
{
    use SoftDeletes;

    protected $table = 'measurements';

    protected $fillable = [
        'unit',
        'netto',
        'brutto',
        'product',
        'customer',
        'plate',
        'driver',
        'modified_by_id',
        'notes'
    ];

    public static function boot() {
        parent::boot();

        static::creating(function (Model $model) {
            $model->modified_by_id = auth()->id();
        });

        static::updating(function (Model $model) {
            $model->modified_by_id = auth()->id();
        });
    }

    public function attachments()
    {
        return $this->hasMany(Attachments::class);
    }

    public function modifiedBy()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Measurement extends Model
{
    use SoftDeletes, HasFactory;

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

        static::deleting(function (Model $model) {
            $model->modified_by_id = auth()->id();
        });
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['from'] ?? null, function ($query, $from) {
            $query->whereDate('from', $from);
        })->when($filters['to'] ?? null, function ($query, $to) {
            $query->whereDate('to', $to);
        })->when($filters['unit'] ?? null, function ($query, $unit) {
            $query->where('unit', 'like', '%'.$unit.'%');
        })->when($filters['product'] ?? null, function ($query, $product) {
            $query->where('product', 'like', '%'.$product.'%');
        })->when($filters['plate'] ?? null, function ($query, $plate) {
            $query->where('plate', 'like', '%'.$plate.'%');
        })->when($filters['customer'] ?? null, function ($query, $customer) {
            $query->where('customer', 'like', '%'.$customer.'%');
        })->when($filters['driver'] ?? null, function ($query, $driver) {
            $query->where('driver', 'like', '%'.$driver.'%');
        })->when($filters['status'] ?? null, function ($query, $status) {
            if ($status == 'all') {
                $query->withTrashed();
            } elseif ($status == 'trashed') {
                $query->onlyTrashed();
            }
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

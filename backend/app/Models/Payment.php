<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\SchoolScope;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'invoice_id',
        'amount_paid',
        'payment_method',
        'transaction_reference',
        'paid_at',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope(new SchoolScope);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
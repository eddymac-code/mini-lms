<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DarajaCallback extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'payload',
        'checkout_request_id',
        'merchant_request_id',
        'mpesa_receipt_number',
        'received_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'received_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }
}

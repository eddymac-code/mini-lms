<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_principal',
        'max_principal',
        'default_principal',
        'interest_rate',
        'interest_method',
        'term',
        'term_unit',
        'repayment_order',
        'fees',
        'fee_is_recurring',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'min_principal' => 'decimal:2',
            'max_principal' => 'decimal:2',
            'default_principal' => 'decimal:2',
            'interest_rate' => 'decimal:4',
            'fees' => 'decimal:2',
            'fee_is_recurring' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}

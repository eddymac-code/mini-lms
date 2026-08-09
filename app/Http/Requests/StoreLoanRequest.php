<?php

namespace App\Http\Requests;

use App\Models\LoanProduct;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id',
            ],

            'loan_product_id' => [
                'required',
                'integer',
                'exists:loan_products,id',
            ],

            'principal' => [
                'required',
                'numeric',
                'min:0.01',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->loan_product_id || ! $this->principal) {
                return;
            }

            $product = LoanProduct::find($this->loan_product_id);

            if (! $product) {
                return;
            }

            if (! $product->is_active) {
                $validator->errors()->add(
                    'loan_product_id',
                    'The selected loan product is inactive.'
                );

                return;
            }

            if (
                $this->principal < $product->min_principal ||
                $this->principal > $product->max_principal
            ) {
                $validator->errors()->add(
                    'principal',
                    'The principal must be between ' .
                    number_format($product->min_principal, 2) .
                    ' and ' .
                    number_format($product->max_principal, 2) .
                    '.'
                );
            }
        });
    }
}

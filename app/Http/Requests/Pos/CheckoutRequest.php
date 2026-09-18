<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('process-pos');
    }

    public function rules(): array
    {
        return [
            'payment_method'   => ['required', 'in:cash,qris,transfer,later'],
            'table_number'     => ['nullable', 'string', 'max:50'],
            'discount'         => ['nullable', 'integer', 'min:0'],
            'include_tax'      => ['nullable', 'boolean'],
            'cash_received'    => ['required_if:payment_method,cash', 'nullable', 'integer', 'min:0'],
            'items'            => ['required', 'array', 'min:1'],
            'items.*.id'       => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
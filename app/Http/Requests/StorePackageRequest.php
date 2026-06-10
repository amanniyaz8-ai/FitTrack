<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'total_sessions' => 'required|integer|min:1|max:100',
            'price'          => 'required|numeric|min:0',
            'payment_date'   => 'required|date',
            'is_paid'        => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'total_sessions.required' => 'Укажите количество тренировок',
            'price.required'          => 'Укажите стоимость пакета',
            'payment_date.required'   => 'Укажите дату оплаты',
        ];
    }
}

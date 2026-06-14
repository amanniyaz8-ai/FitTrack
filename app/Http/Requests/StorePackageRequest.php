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
            'training_type'  => 'nullable|in:personal,mini_group',
            'training_days'  => 'nullable|array',
            'training_days.*'=> 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
            'training_time'  => 'nullable|date_format:H:i',
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

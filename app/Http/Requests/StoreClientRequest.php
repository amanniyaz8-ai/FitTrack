<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'full_name'         => 'required|string|max:255',
            'phone'             => 'nullable|string|max:20',
            'goal'              => 'nullable|string|max:1000',
            'contraindications' => 'nullable|string|max:1000',
            'training_days'     => 'required|array|min:1',
            'training_days.*'   => 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
            'training_time'     => 'nullable|date_format:H:i',
            'training_type'     => 'required|in:personal,mini_group',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'    => 'ФИО клиента обязательно',
            'training_days.required' => 'Выберите хотя бы один день тренировок',
            'training_days.min'     => 'Выберите хотя бы один день тренировок',
        ];
    }
}

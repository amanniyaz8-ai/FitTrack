<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSessionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'status'         => 'required|in:scheduled,completed,missed,cancelled',
            'notes'          => 'nullable|string|max:1000',
            'scheduled_time' => 'nullable|date_format:H:i',
        ];
    }
}

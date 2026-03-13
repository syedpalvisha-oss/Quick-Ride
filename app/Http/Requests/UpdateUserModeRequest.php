<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserModeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => [
                'present',
                'nullable',
                'integer',
                Rule::exists('vehicles', 'id')->where(
                    fn ($query) => $query
                        ->where('user_id', $this->user()->getKey())
                        ->whereNull('deleted_at')
                ),
            ],
        ];
    }
}

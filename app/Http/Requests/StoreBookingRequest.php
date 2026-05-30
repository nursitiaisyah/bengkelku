<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'client_id'             => 'required|integer|exists:clients,id',
            'items'                 => 'required|array|min:1',
            'items.*.service_id'    => 'required|integer|exists:services,id',
            'items.*.quantity'      => 'required|integer|min:1',
        ];
    }
}

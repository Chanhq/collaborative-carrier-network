<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransportRequestRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'requester_name' => 'required|string|max:256',
            'origin_x' => 'required|Integer|min:0|max:250',
            'origin_y' => 'required|Integer|min:0|max:250',
            'destination_x' => 'required|Integer|min:0|max:250',
            'destination_y' => 'required|Integer|min:0|max:250',
        ];
    }
}

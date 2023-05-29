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
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'requester_name' => 'required|string|max:256',
            'origin_node' => 'required|Integer|exists:map_vertices,id',
            'destination_node' => 'required|Integer|exists:map_vertices,id|different:origin_node',
        ];
    }
}

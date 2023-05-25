<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'requester_name' => ['required', 'string', 'max:256'],
            'origin' => ['required', 'string', 'regex:/\(\d+, \d+\)/'],
            'destination' => ['required', 'string', 'regex:/\(\d+, \d+\)/'],
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $input = $validator->validated();
                $origin = $input['origin'];
                $destination = $input['destination'];

                // transform input: "(originX, originY), (destX, destY) -> [originX, originY, destX, destY]"
                $originCoords = array_map(trim(...), explode(',', substr($origin, 1, -1)));

                $originValidationResults = array_map(function (string $coord) {
                    return ((int)$coord) <= 250;
                }, $originCoords);

                if (!array_reduce($originValidationResults, function (bool $carry, bool $value) {return $carry && $value;}, true)) {
                    $validator->errors()->add('origin', 'The coordinates of origin exceed the value range (max 250)');
                }

                // transform input: "(originX, originY), (destX, destY) -> [originX, originY, destX, destY]"
                $destinationCoords = array_map(trim(...), explode(',', substr($destination, 1, -1)));

                $destinationValidationResults = array_map(function (string $coord) {
                    return ((int)$coord) <= 250;
                }, $destinationCoords);

                if (!array_reduce($destinationValidationResults, function (bool $carry, bool $value) {return $carry && $value;}, true)) {
                    $validator->errors()->add('destination', 'The coordinates of destination exceed the value range (max 250)');
                }
            }
        ];
    }
}

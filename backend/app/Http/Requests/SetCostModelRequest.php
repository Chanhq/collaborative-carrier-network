<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetCostModelRequest extends FormRequest
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
            'transport_request_minimum_revenue' =>
                'required_without_all:transport_request_cost_base,transport_request_cost_variable,' .
                'transport_request_price_base,transport_request_price_variable|numeric|integer',
            'transport_request_cost_base' =>
                'required_without_all:transport_request_minimum_revenue,transport_request_cost_variable,' .
                'transport_request_price_base,transport_request_price_variable|numeric|integer',
            'transport_request_cost_variable' =>
                'required_without_all:transport_request_minimum_revenue,transport_request_cost_base,' .
                'transport_request_price_base,transport_request_price_variable|numeric|integer',
            'transport_request_price_base' =>
                'required_without_all:transport_request_minimum_revenue,transport_request_cost_base,' .
                'transport_request_cost_variable,transport_request_price_variable|numeric|integer',
            'transport_request_price_variable' =>
                'required_without_all:transport_request_minimum_revenue,transport_request_cost_base,' .
                'transport_request_cost_variable,transport_request_price_base|numeric|integer',
        ];
    }
}

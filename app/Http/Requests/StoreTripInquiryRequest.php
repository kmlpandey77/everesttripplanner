<?php

namespace App\Http\Requests;

use App\Enums\TravelStyle;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTripInquiryRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'travel_style' => ['nullable', Rule::enum(TravelStyle::class)],
            'trip_id' => ['nullable', 'integer', 'exists:trips,id'],
            'preferred_start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'group_size' => ['nullable', 'integer', 'min:1', 'max:100'],
            'budget_range' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
        ];
    }
}

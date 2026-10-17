<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContributionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => 'required|in:new_proverb,translation,meaning,equivalent,usage,origin,remix',
            'payload' => 'required|array',
            'payload.text' => 'sometimes|string|max:2000',
            'proverb_id' => 'nullable|exists:proverbs,id',
            'license_ack' => 'accepted',
            'website' => 'prohibited', // honeypot
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $payload = $this->input('payload', []);
            $text = json_encode($payload);
            if (preg_match('/https?:\/\//i', $text)) {
                $validator->errors()->add('payload', 'URLs are not allowed');
            }
        });
    }
}

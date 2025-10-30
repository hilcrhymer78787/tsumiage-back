<?php

namespace App\Http\Requests;

use App\Http\Requests\Base\BaseFormRequest;

class TaskReadRequest extends BaseFormRequest
{
    /**
     * バリデーション前にリクエストを整形
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_only_trashed')) {
            $this->merge([
                'is_only_trashed' => filter_var($this->input('is_only_trashed'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer',
            'date' => 'required|date_format:Y-m-d',
            'is_only_trashed' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'user_id は必須です。',
            'user_id.integer' => 'user_id は整数でなければなりません。',
            'date.required' => 'date は必須です。',
            'date.date_format' => 'date は YYYY-MM-DD 形式で入力してください。',
            'is_only_trashed.boolean' => 'is_only_trashed は真偽値で指定してください。',
        ];
    }
}

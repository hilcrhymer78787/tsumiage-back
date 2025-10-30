<?php

namespace App\Http\Requests;

use App\Http\Requests\Base\BaseFormRequest;

class TaskDeleteRequest extends BaseFormRequest
{
    /**
     * バリデーション前にリクエストを整形
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_hard_delete')) {
            $this->merge([
                'is_hard_delete' => filter_var($this->input('is_hard_delete'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'is_hard_delete' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'id.integer' => 'id は整数でなければなりません。',
            'id.required' => 'id は必須です。',
            'is_hard_delete.boolean' => 'is_hard_delete は真偽値で指定してください。',
        ];
    }
}

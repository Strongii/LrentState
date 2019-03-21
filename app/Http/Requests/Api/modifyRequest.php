<?php

namespace App\Http\Requests\api;

use Illuminate\Foundation\Http\FormRequest;

class modifyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:2|max:4',
            'phone' => 'required|regex:/^1[34578]\d{9}$/',
        ];
    }
}

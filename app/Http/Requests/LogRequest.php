<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class LogRequest extends Request
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
            'log_name' => 'required|max:50',
            'site' => 'required|max:50',
            'content' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'required' => 'O campo :attribute não pode ser vazio.'
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'column_id' => 'required|integer',
            'status' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'The title field is required.',
            'column_id.required' => 'The column ID field is required.',
            'status.required' => 'The status field is required.',
        ];
    }
}

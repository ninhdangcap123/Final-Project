<?php

namespace App\Http\Requests\MyCourse;

use Illuminate\Foundation\Http\FormRequest;

class CourseCreate extends FormRequest
{

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
            'name' => 'required|string|min:3',
            'major_id' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'major_id' => 'Major',
        ];
    }

}

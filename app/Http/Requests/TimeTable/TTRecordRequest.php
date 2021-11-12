<?php

namespace App\Http\Requests\TimeTable;

use Illuminate\Foundation\Http\FormRequest;

class TTRecordRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        if( $this->method() === 'POST' ) {
            return [
                'name' => 'required|string|min:3',
                'my_course_id' => 'required',
            ];
        }

        return [
            'name' => 'required|string|min:3'.$this->ttr,
            'my_course_id' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'my_course_id' => 'Course',
        ];
    }

}

<?php

namespace App\Http\Requests\Mark;

use Illuminate\Foundation\Http\FormRequest;

class MarkSelector extends FormRequest
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
            'exam_id' => 'required',
            'my_course_id' => 'required',
            'section_id' => 'required',
            'subject_id' => 'required',
        ];
    }

    public function attributes()
    {
        return  [
            'exam_id' => 'Exam',
            'my_course_id' => 'Course',
            'section_id' => 'Section',
            'subject_id' => 'Subject',
        ];
    }
}

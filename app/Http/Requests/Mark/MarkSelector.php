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
            'exam_id' => 'required|exists:exams,id',
            'my_course_id' => 'required|exists:my_courses,id',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
        ];
    }

    public function attributes()
    {
        return [
            'exam_id' => 'Exam',
            'my_course_id' => 'Courses',
            'class_id' => 'Classes',
            'subject_id' => 'Subject',
        ];
    }
}

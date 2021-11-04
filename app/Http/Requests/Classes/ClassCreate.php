<?php

namespace App\Http\Requests\Classes;

use App\Helpers\DisplayMessageHelper;
use App\Helpers\Qs;
use Illuminate\Foundation\Http\FormRequest;

class ClassCreate extends FormRequest
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
            'name' => 'required|string',
            'my_course_id' => 'required',
            'teacher_id' => 'sometimes|nullable',
        ];
    }

    public function attributes()
    {
        return  [
            'my_course_id' => 'Course',
            'teacher_id' => 'Teacher',
        ];
    }

    protected function getValidatorInstance()
    {
        $input = $this->all();

        $input['teacher_id'] = $input['teacher_id'] ? DisplayMessageHelper::decodeHash($input['teacher_id']) : NULL;

        $this->getInputSource()->replace($input);

        return parent::getValidatorInstance();
    }

}

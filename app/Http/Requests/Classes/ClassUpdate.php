<?php

namespace App\Http\Requests\Classes;

use App\Helpers\DisplayMessageHelper;
use Illuminate\Foundation\Http\FormRequest;

class ClassUpdate extends FormRequest
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
            'teacher_id' => 'sometimes|nullable',
        ];
    }

    public function attributes()
    {
        return [
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

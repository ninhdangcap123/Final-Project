<?php

namespace App\Http\Requests\TimeTable;

use Illuminate\Foundation\Http\FormRequest;

class TTRequest extends FormRequest
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
        return [
            'exam_date' => 'sometimes|required|string|min:8',
            'day' => 'sometimes|required|string|min:6',
            'subject_id' => 'required',
            'ttr_id' => 'required',
            'ts_id' => 'required',
        ];
    }

    public function attributes()
    {
        return  [
            'subject_id' => 'Subject',
            'ttr_id' => 'TimeTable Record',
            'ts_id' => 'Time Slot',
        ];
    }

}

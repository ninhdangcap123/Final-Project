<?php

namespace App\Http\Requests\Mark;

use App\Models\Mark;
use Illuminate\Foundation\Http\FormRequest;

class MarkUpdate extends FormRequest
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


        ];

    }

}

<?php

namespace App\Rules;

use App\Repositories\Mark\MarkRepositoryInterface;
use Illuminate\Contracts\Validation\Rule;

class MarkUpdateRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $markRepo;
    public function __construct(MarkRepositoryInterface $markRepo)
    {
        return $this->markRepo = $markRepo;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}

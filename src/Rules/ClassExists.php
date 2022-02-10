<?php

namespace BrilliantPortal\Framework\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class ClassExists implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        if (Str::of($value)->startsWith('\\')) {
            return class_exists($value);
        } else {
            return class_exists('\App\Models\\' . $value);
        }
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

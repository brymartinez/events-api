<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class OnceOffEndDateTimeRule implements Rule
{
    public function passes($attribute, $value)
    {
        // Get the request instance
        $request = request();

        if ($request->input('frequency') === 'Once-Off') {
            return $value === null;
        }

        return $value !== null;
    }

    public function message()
    {
        return 'The end date time field is required unless the frequency is Once-Off.';
    }
}

<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class ValidInviteesRule implements Rule
{
    public function passes($attribute, $value)
    {
        return collect($value)->every(function ($userId) {
            return User::where('id', $userId)->exists();
        });
    }

    public function message()
    {
        return 'One or more invitees are invalid.';
    }
}

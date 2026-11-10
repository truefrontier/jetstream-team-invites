<?php

namespace App\Actions\Fortify;

trait PasswordValidationRules
{
    protected function passwordRules()
    {
        return ['required', 'string', 'min:8', 'confirmed'];
    }
}

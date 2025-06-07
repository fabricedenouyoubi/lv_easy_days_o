<?php

namespace Modules\Entreprise\app\Rules;

use Illuminate\Contracts\Validation\Rule;

class TelephoneRule implements Rule
{
    public function passes($attribute, $value)
    {
        // Vérifier que c'est uniquement des chiffres et le signe +
        return preg_match('/^[0-9+]+$/', $value);
    }

    public function message()
    {
        return 'Le :attribute doit contenir uniquement des chiffres et le signe +.';
    }
}
<?php

namespace Modules\Budget\app\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Budget\Models\AnneeFinanciere;

class ValideDateAnneeFinanciere implements Rule
{
    private $type;
    private $dateDebut;

    public function __construct($type, $dateDebut = null)
    {
        $this->type = $type; 
        $this->dateDebut = $dateDebut;
    }

    public function passes($attribute, $value)
    {
        if ($this->type === 'debut') {
            return AnneeFinanciere::isValideDateDebut($value);
        }

        if ($this->type === 'fin' && $this->dateDebut) {
            return AnneeFinanciere::isValideDateFin($this->dateDebut, $value);
        }

        return false;
    }

    public function message()
    {
        if ($this->type === 'debut') {
            return 'La date de début doit être le 1er avril.';
        }

        return 'La date de fin doit être le 31 mars de l\'année suivante.';
    }
}
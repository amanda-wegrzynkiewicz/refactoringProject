<?php

namespace App\Helpers;

class CountryCodeValidator
{
    private $europeanCoutryCodes = [
        'AT',
        'BE',
        'BG',
        'CY',
        'CZ',
        'DE',
        'DK',
        'EE',
        'ES',
        'FI',
        'FR',
        'GR',
        'HR',
        'HU',
        'IE',
        'IT',
        'LT',
        'LU',
        'LV',
        'MT',
        'NL',
        'PO',
        'PT',
        'RO',
        'SE',
        'SI',
        'SK',
    ];

    public function europeanCountryCodechecker(string $countryCode): bool
    {
        return in_array($countryCode, $this->europeanCoutryCodes);
    }
}

<?php

namespace App\API;

use App\Interfaces\PaymentCardDetailsProviderInterface;

class LookupApiProvider implements PaymentCardDetailsProviderInterface
{
    private const LOOKUP_URL = 'https://lookup.binlist.net/';

    public function getCountryCodeByBIN(string $bin): string|null
    {
        $paymentCardDetailsData = json_decode(file_get_contents(self::LOOKUP_URL . $bin));
        return $paymentCardDetailsData->country->alpha2 ?? NULL;
    }
}

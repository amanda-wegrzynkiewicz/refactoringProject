<?php

namespace App\API;

use App\Interfaces\PaymentCardDetailsProviderInterface;

class LookupApiProvider implements PaymentCardDetailsProviderInterface
{
    const LINK = 'https://lookup.binlist.net/';

    public function getCountryCodeByBIN($bin)
    {
        return $this->getCardDetailsData($bin)->country->alpha2 ?? NULL;
    }

    private function getCardDetailsData($bin)
    {
        $cardDetailsData = json_decode(file_get_contents(self::LINK . $bin));
        return $cardDetailsData ?? NULL;
    }
}

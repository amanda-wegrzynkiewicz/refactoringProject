<?php

namespace App\API;

use App\Interfaces\PaymentCardDetailsProviderInterface;

class LookupApiProvider implements PaymentCardDetailsProviderInterface
{
    private const LOOKUP_URL = 'https://lookup.binlist.net/';
    private $bin;

    public function setPaymentBIN(string $bin): ?string {
        return $this->bin = $bin;
    }

    public function getCountryCode(): ?string
    {
        return $this->getCountryCodeByBIN($this->bin);
    }

    private function getCountryCodeByBIN(string $bin): ?string
    {
        $paymentCardDetailsData = json_decode(file_get_contents(self::LOOKUP_URL . $bin));
        return $paymentCardDetailsData->country->alpha2 ?? NULL;
    }
}

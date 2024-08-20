<?php

namespace App\API;

use App\Interfaces\PaymentCardDetailsProviderInterface;

class LookupApiProvider implements PaymentCardDetailsProviderInterface
{
    private const LOOKUP_URL = 'https://lookup.binlist.net/';
    private $bin;

    public function setPaymentBIN(string $bin): string|null {
        return $this->bin = $bin;
    }

    public function getCountryCode(): string|null
    {
        return $this->getCountryCodeByBIN($this->bin);
    }

    private function getCountryCodeByBIN(string $bin): string|null
    {
        $paymentCardDetailsData = json_decode(file_get_contents(self::LOOKUP_URL . $bin));
        return $paymentCardDetailsData->country->alpha2 ?? NULL;
    }
}

<?php

namespace App\Interfaces;

interface PaymentCardDetailsProviderInterface
{
    public function getCountryCodeByBIN(string $bin): ?string;
}

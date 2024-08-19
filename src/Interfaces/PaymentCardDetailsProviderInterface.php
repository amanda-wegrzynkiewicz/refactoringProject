<?php

namespace App\Interfaces;

interface PaymentCardDetailsProviderInterface
{
    function getCountryCodeByBIN(string $bin): ?string;
}

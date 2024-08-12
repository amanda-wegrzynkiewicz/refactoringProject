<?php

namespace App\Interfaces;

interface PaymentCardDetailsProviderInterface
{
    function getCountryCodeByBIN($bin);
}

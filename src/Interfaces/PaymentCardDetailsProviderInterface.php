<?php

namespace App\Interfaces;

interface PaymentCardDetailsProviderInterface
{
    function setPaymentBIN(string $bin): string|null;

    function getCountryCode(): string|null;
}

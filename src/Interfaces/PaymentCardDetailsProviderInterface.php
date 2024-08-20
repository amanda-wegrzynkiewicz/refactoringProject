<?php

namespace App\Interfaces;

interface PaymentCardDetailsProviderInterface
{
    function setPaymentBIN(string $bin): ?string;

    function getCountryCode(): ?string;
}

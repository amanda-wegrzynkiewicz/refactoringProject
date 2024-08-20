<?php

namespace App\Interfaces;

interface ExchangeRatesProviderInterface 
{
    public function getExchangeRates (string $baseCurrency): float;
}
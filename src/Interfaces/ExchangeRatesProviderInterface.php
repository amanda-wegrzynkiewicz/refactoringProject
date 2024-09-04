<?php

namespace App\Interfaces;

use App\Dto\ExchangeRatesProviderDto;
interface ExchangeRatesProviderInterface 
{
    public function getExchangeRateByCurrency(string $currency): ExchangeRatesProviderDto;
}
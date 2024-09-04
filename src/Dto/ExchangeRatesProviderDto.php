<?php

namespace App\Dto;

class ExchangeRatesProviderDto
{
    public function __construct(
        private string $currency, 
        private float $rate
    ) {}

    public function getRate()
    {
        return $this->rate;
    }

    public function getCurrency()
    {
        return $this->currency;
    }
}
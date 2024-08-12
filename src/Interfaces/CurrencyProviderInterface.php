<?php

namespace App\Interfaces;

interface CurrencyProviderInterface 
{
    public function getCurrency ($baseCurrency);
}
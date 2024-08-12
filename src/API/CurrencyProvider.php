<?php

namespace App\API;

use App\Interfaces\CurrencyProviderInterface;

class CurrencyProvider implements CurrencyProviderInterface
{
    const API_LINK = 'http://api.exchangeratesapi.io/latest';
    private $apiKey;
    private $apiLink;

    function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->apiLink = self::API_LINK . '?access_key=' . $this->apiKey;
    }

    public function getCurrency($baseCurrency)
    {
        $currencyDataResponse = $this->getCurrencyData();
        return $currencyDataResponse['rates'][$baseCurrency];
    }

    private function getCurrencyData()
    {
        return json_decode(file_get_contents($this->apiLink), true);
    }
}

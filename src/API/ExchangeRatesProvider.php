<?php

namespace App\API;

use App\Interfaces\ExchangeRatesProviderInterface;

class ExchangeRatesProvider implements ExchangeRatesProviderInterface
{
    const API_LINK = 'http://api.exchangeratesapi.io/latest';
    private $apiKey;
    private $apiLink;

    function __construct()
    {
        $this->apiKey = getenv('EXCHANGERATES_API_KEY') ?? throw new \InvalidArgumentException("Invalid ENV value EXCHANGERATES_API_KEY!");
        $this->apiLink = self::API_LINK . '?access_key=' . $this->apiKey;
    }

    public function getExchangeRates(string $baseCurrency): float
    {
        $currencyDataResponse = $this->getExternalExchangeRates();
        return $currencyDataResponse['rates'][$baseCurrency];
    }

    private function getExternalExchangeRates(): array
    {
        return json_decode(file_get_contents($this->apiLink), true);
    }
}

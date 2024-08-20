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
        $this->apiKey = getenv('EXCHANGERATES_API_KEY') ?? NULL;
        if (!$this->apiKey) {
            throw new \Exception("Invalid ENV value EXCHANGERATES_API_KEY!");
        }
        $this->apiLink = self::API_LINK . '?access_key=' . $this->apiKey;
    }

    public function getExchangeRates(string $baseCurrency): float
    {
        $currencyDataResponse = $this->getCurrencyData();
        return $currencyDataResponse['rates'][$baseCurrency];
    }

    private function getCurrencyData(): array
    {
        return json_decode(file_get_contents($this->apiLink), true);
    }
}

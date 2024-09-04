<?php

namespace App\API;

use App\Interfaces\ExchangeRatesProviderInterface;
use App\Dto\ExchangeRatesProviderDto;

class ExchangeRatesProvider implements ExchangeRatesProviderInterface
{
    const API_LINK = 'http://api.exchangeratesapi.io/latest';
    private $apiKey;
    private $apiLink;

    public function __construct()
    {
        $this->apiKey = getenv('EXCHANGERATES_API_KEY') ?? throw new \InvalidArgumentException("Invalid ENV value EXCHANGERATES_API_KEY!");
        $this->apiLink = self::API_LINK . '?access_key=' . $this->apiKey;
    }

    public function getExchangeRateByCurrency(string $currency): ExchangeRatesProviderDto
    {
        $currencyDataResponse = json_decode(file_get_contents($this->apiLink), true);
        $rate = $currencyDataResponse['rates'][$currency] ?? throw new \InvalidArgumentException("There is no given currency on currencies list");
        return new ExchangeRatesProviderDto($currency, $rate);
    }
}

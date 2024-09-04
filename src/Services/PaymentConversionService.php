<?php

namespace App\Services;

use App\Helpers\CountryCodeValidator;
use App\Interfaces\ExchangeRatesProviderInterface;
use App\Interfaces\PaymentCardDetailsProviderInterface;

class PaymentConversionService
{
    public function __construct(
        private CountryCodeValidator $countryCodeValidator,
        private PaymentCardDetailsProviderInterface $cardDetailsProvider,
        private ExchangeRatesProviderInterface $currencyProvider,
    ) {}

    public function calculateTotalCommission(object $paymentData): float
    {
        $paymentCountryCode = $this->cardDetailsProvider->getCountryCodeByBIN($paymentData->bin) ?? throw new \Exception("Payment Card Provider response error!");

        $isEuropeanPayment = $this->countryCodeValidator->europeanCountryCodechecker($paymentCountryCode);
        $currencyRate = $this->currencyProvider->getExchangeRateByCurrency($paymentData->currency)->getRate();

        if (!$currencyRate || $currencyRate < 0) {
            throw new \Exception("Currency API Provider response error!");
        }

        $paymentAmount = $paymentData->amount;

        if ($currencyRate > 0) {
            $paymentAmount = $paymentAmount / $currencyRate;
        }

        $paymentAmountSummary = $paymentAmount * ($isEuropeanPayment ? 0.01 : 0.02);
        return (float)number_format($paymentAmountSummary, 2, '.', '');
    }
}

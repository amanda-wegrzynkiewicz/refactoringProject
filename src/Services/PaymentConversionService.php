<?php

namespace App\Services;

use App\Interfaces\CurrencyProviderInterface;
use App\Interfaces\PaymentCardDetailsProviderInterface;
use App\Helpers\CountryCodeValidator;

class PaymentConversionService
{
    public function __construct(
        private PaymentCardDetailsProviderInterface $cardDetailsProvider,
        private CountryCodeValidator $countryCodeValidator,
        private CurrencyProviderInterface $currencyProvider,
    ) { }

    public function calculateTotalCommission(object $paymentData): float
    {
        $paymentCountryCode = $this->cardDetailsProvider->getCountryCodeByBIN($paymentData->bin);
        
        if (empty($paymentCountryCode)) {
            throw new \Exception("Payment Card Provider response error!");
        }
        
        $isEuropeanPayment = $this->countryCodeValidator->europeanCountryCodechecker($paymentCountryCode);
        $currencyRate = $this->currencyProvider->getCurrency($paymentData->currency);
        
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


<?php

namespace App\Tests\Services;

use PHPUnit\Framework\TestCase;

use App\Interfaces\ExchangeRatesProviderInterface;
use App\Interfaces\PaymentCardDetailsProviderInterface;
use App\Helpers\CountryCodeValidator;
use App\Services\PaymentConversionService;

class PaymentConversionServiceTest extends TestCase
{
    public function testCalculatesAmountForEuropeanPaymentInEuroCurrency()
    {
        $paymentData = (object)[
            'bin' => '45717360',
            'amount' => 100,
            'currency' => 'EUR'
        ];

        $cardDetailsProvider = $this->createMock(PaymentCardDetailsProviderInterface::class);
        $cardDetailsProvider
            ->method('getCountryCode')
            ->willReturn('DK');

        $countryCodeValidator = $this->createMock(CountryCodeValidator::class);
        $countryCodeValidator
            ->method('europeanCountryCodechecker')
            ->with('DK')
            ->willReturn(true);

        $currencyProvider = $this->createMock(ExchangeRatesProviderInterface::class);
        $currencyProvider
            ->method('getExchangeRates')
            ->with('EUR')
            ->willReturn(1.0);

        $test = new PaymentConversionService(
            $countryCodeValidator,
            $cardDetailsProvider,
            $currencyProvider
        );

        $result = $test->calculateTotalCommission($paymentData);
        $this->assertEquals(1.0, $result);
    }

    public function testCalculatesAmountForEuropeanPaymentInNonEuroCurrency()
    {
        $paymentData = (object)[
            'bin' => '45717360',
            'amount' => 9.55,
            'currency' => 'USD'
        ];

        $cardDetailsProvider = $this->createMock(PaymentCardDetailsProviderInterface::class);
        $cardDetailsProvider
            ->method('getCountryCode')
            ->willReturn('PO');

        $countryCodeValidator = $this->createMock(CountryCodeValidator::class);
        $countryCodeValidator
            ->method('europeanCountryCodechecker')
            ->with('PO')
            ->willReturn(true);

        $currencyProvider = $this->createMock(ExchangeRatesProviderInterface::class);
        $currencyProvider
            ->method('getExchangeRates')
            ->with('USD')
            ->willReturn(1.09);

        $test = new PaymentConversionService(
            $countryCodeValidator,
            $cardDetailsProvider,
            $currencyProvider
        );

        $result = $test->calculateTotalCommission($paymentData);
        $this->assertEquals(0.09, $result);
    }

    public function testCalculatesAmountForNonEuropeanPaymentInEuroCurrency()
    {
        $paymentData = (object)[
            'bin' => '516793',
            'amount' => 12.67,
            'currency' => 'EUR'
        ];

        $cardDetailsProvider = $this->createMock(PaymentCardDetailsProviderInterface::class);
        $cardDetailsProvider
            ->method('getCountryCode')
            ->willReturn('JPY');

        $countryCodeValidator = $this->createMock(CountryCodeValidator::class);
        $countryCodeValidator
            ->method('europeanCountryCodechecker')
            ->with('JPY')
            ->willReturn(false);

        $currencyProvider = $this->createMock(ExchangeRatesProviderInterface::class);
        $currencyProvider
            ->method('getExchangeRates')
            ->with('EUR')
            ->willReturn(1.0);

        $test = new PaymentConversionService(
            $countryCodeValidator,
            $cardDetailsProvider,
            $currencyProvider
        );
        $result = $test->calculateTotalCommission($paymentData);
        $this->assertEquals(0.25, $result);
    }


    public function testCalculatesAmountForNonEuropeanPaymentInNonEuroCurrency()
    {
        $paymentData = (object)[
            'bin' => '516793',
            'amount' => 10000,
            'currency' => 'JPY'
        ];

        $cardDetailsProvider = $this->createMock(PaymentCardDetailsProviderInterface::class);
        $cardDetailsProvider
            ->method('getCountryCode')
            ->willReturn('JP');

        $countryCodeValidator = $this->createMock(CountryCodeValidator::class);
        $countryCodeValidator
            ->method('europeanCountryCodechecker')
            ->with('JP')
            ->willReturn(false);

        $currencyProvider = $this->createMock(ExchangeRatesProviderInterface::class);
        $currencyProvider
            ->method('getExchangeRates')
            ->with('JPY')
            ->willReturn(160.438587);

        $test = new PaymentConversionService(
            $countryCodeValidator,
            $cardDetailsProvider,
            $currencyProvider
        );
        $result = $test->calculateTotalCommission($paymentData);
        $this->assertEquals(1.25, $result);
    }

    public function testHandlesMissingCountryCode()
    {
        $paymentData = (object)[
            'bin' => '123456',
            'amount' => 100,
            'currency' => 'USD'
        ];

        $cardDetailsProvider = $this->createMock(PaymentCardDetailsProviderInterface::class);
        $cardDetailsProvider
            ->method('getCountryCode')
            ->willReturn(null);

        $countryCodeValidator = $this->createMock(CountryCodeValidator::class);
        $currencyProvider = $this->createMock(ExchangeRatesProviderInterface::class);

        $this->expectExceptionMessage('Payment Card Provider response error!');

        $test = new PaymentConversionService(
            $countryCodeValidator,
            $cardDetailsProvider,
            $currencyProvider
        );
        $test->calculateTotalCommission($paymentData);
    }

    public function testHandlesMissingCurrencyRate()
    {
        $paymentData = (object)[
            'bin' => '123456',
            'amount' => 100,
            'currency' => 'USD'
        ];

        $cardDetailsProvider = $this->createMock(PaymentCardDetailsProviderInterface::class);
        $cardDetailsProvider
            ->method('getCountryCode')
            ->willReturn('US');

        $countryCodeValidator = $this->createMock(CountryCodeValidator::class);
        $countryCodeValidator
            ->method('europeanCountryCodechecker')
            ->with('US')
            ->willReturn(false);

        $currencyProvider = $this->createMock(ExchangeRatesProviderInterface::class);
        $currencyProvider
            ->method('getExchangeRates')
            ->with('USD')
            ->willReturn(0.0);

        $this->expectExceptionMessage('Currency API Provider response error!');

        $test = new PaymentConversionService(
            $countryCodeValidator,
            $cardDetailsProvider,
            $currencyProvider
        );
        $test->calculateTotalCommission($paymentData);
    }
}

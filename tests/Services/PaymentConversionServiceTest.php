<?php

namespace App\Tests\Services;

use PHPUnit\Framework\TestCase;

use App\Interfaces\CurrencyProviderInterface;
use App\Interfaces\PaymentCardDetailsProviderInterface;
use App\Helpers\CountryCodeValidator;
use App\Services\PaymentConversionService;

class PaymentConversionServiceTest extends TestCase
{
    public function testXCalculatesAmountForEuropeanPaymentInEuroCurrency()
    {
        $paymentData = (object)[
            'bin' => '45717360',
            'amount' => 100,
            'currency' => 'EUR'
        ];

        $cardDetailsProvider = $this->createMock(PaymentCardDetailsProviderInterface::class);
        $cardDetailsProvider
            ->method('getCountryCodeByBIN')
            ->willReturn('DK');

        $countryCodeValidator = $this->createMock(CountryCodeValidator::class);
        $countryCodeValidator
            ->method('europeanCountryCodechecker')
            ->with('DK')
            ->willReturn(true);

        $currencyProvider = $this->createMock(CurrencyProviderInterface::class);
        $currencyProvider
            ->method('getCurrency')
            ->with('EUR')
            ->willReturn(1);

        $x = new PaymentConversionService(
            $cardDetailsProvider,
            $countryCodeValidator,
            $currencyProvider
        );

        $result = $x->calculateTotalCommission($paymentData);
        $this->assertEquals(1.0, $result); // 100 * 0.01 = 1
    }

    public function testXCalculatesAmountForEuropeanPaymentInNonEuroCurrency()
    {
        $paymentData = (object)[
            'bin' => '45717360',
            'amount' => 9.55,
            'currency' => 'USD'
        ];

        $cardDetailsProvider = $this->createMock(PaymentCardDetailsProviderInterface::class);
        $cardDetailsProvider
            ->method('getCountryCodeByBIN')
            ->willReturn('PO');

        $countryCodeValidator = $this->createMock(CountryCodeValidator::class);
        $countryCodeValidator
            ->method('europeanCountryCodechecker')
            ->with('PO')
            ->willReturn(true);

        $currencyProvider = $this->createMock(CurrencyProviderInterface::class);
        $currencyProvider
            ->method('getCurrency')
            ->with('USD')
            ->willReturn(1.09);

        $x = new PaymentConversionService(
            $cardDetailsProvider,
            $countryCodeValidator,
            $currencyProvider
        );

        $result = $x->calculateTotalCommission($paymentData);
        $this->assertEquals(0.09, $result);
    }

    public function testXCalculatesAmountForNonEuropeanPaymentInEuroCurrency()
    {
        $paymentData = (object)[
            'bin' => '516793',
            'amount' => 12.67,
            'currency' => 'EUR'
        ];

        $cardDetailsProvider = $this->createMock(PaymentCardDetailsProviderInterface::class);
        $cardDetailsProvider
            ->method('getCountryCodeByBIN')
            ->willReturn('JPY');

        $countryCodeValidator = $this->createMock(CountryCodeValidator::class);
        $countryCodeValidator
            ->method('europeanCountryCodechecker')
            ->with('JPY')
            ->willReturn(false);

        $currencyProvider = $this->createMock(CurrencyProviderInterface::class);
        $currencyProvider
            ->method('getCurrency')
            ->with('EUR')
            ->willReturn(1);

        $x = new PaymentConversionService(
            $cardDetailsProvider,
            $countryCodeValidator,
            $currencyProvider
        );
        $result = $x->calculateTotalCommission($paymentData);
        $this->assertEquals(0.25, $result);
    }


    public function testXCalculatesAmountForNonEuropeanPaymentInNonEuroCurrency()
    {
        $paymentData = (object)[
            'bin' => '516793',
            'amount' => 10000,
            'currency' => 'JPY'
        ];

        $cardDetailsProvider = $this->createMock(PaymentCardDetailsProviderInterface::class);
        $cardDetailsProvider
            ->method('getCountryCodeByBIN')
            ->willReturn('JP');

        $countryCodeValidator = $this->createMock(CountryCodeValidator::class);
        $countryCodeValidator
            ->method('europeanCountryCodechecker')
            ->with('JP')
            ->willReturn(false);

        $currencyProvider = $this->createMock(CurrencyProviderInterface::class);
        $currencyProvider
            ->method('getCurrency')
            ->with('JPY')
            ->willReturn(160.438587);

        $x = new PaymentConversionService(
            $cardDetailsProvider,
            $countryCodeValidator,
            $currencyProvider
        );
        $result = $x->calculateTotalCommission($paymentData);
        $this->assertEquals(1.25, $result);
    }

    public function testXHandlesMissingCountryCode()
    {
        $paymentData = (object)[
            'bin' => '123456',
            'amount' => 100,
            'currency' => 'USD'
        ];

        $cardDetailsProvider = $this->createMock(PaymentCardDetailsProviderInterface::class);
        $cardDetailsProvider
            ->method('getCountryCodeByBIN')
            ->willReturn(null);

        $countryCodeValidator = $this->createMock(CountryCodeValidator::class);
        $currencyProvider = $this->createMock(CurrencyProviderInterface::class);

        $this->expectExceptionMessage('Payment Card Provider response error!');

        $x = new PaymentConversionService(
            $cardDetailsProvider,
            $countryCodeValidator,
            $currencyProvider
        );
        $x->calculateTotalCommission($paymentData);
    }

    public function testXHandlesMissingCurrencyRate()
    {
        $paymentData = (object)[
            'bin' => '123456',
            'amount' => 100,
            'currency' => 'USD'
        ];

        $cardDetailsProvider = $this->createMock(PaymentCardDetailsProviderInterface::class);
        $cardDetailsProvider
            ->method('getCountryCodeByBIN')
            ->willReturn('US');

        $countryCodeValidator = $this->createMock(CountryCodeValidator::class);
        $countryCodeValidator
            ->method('europeanCountryCodechecker')
            ->with('US')
            ->willReturn(false);

        $currencyProvider = $this->createMock(CurrencyProviderInterface::class);
        $currencyProvider
            ->method('getCurrency')
            ->with('USD')
            ->willReturn(0);

        $this->expectExceptionMessage('Currency API Provider response error!');

        $x = new PaymentConversionService(
            $cardDetailsProvider,
            $countryCodeValidator,
            $currencyProvider
        );
        $x->calculateTotalCommission($paymentData);
    }
}

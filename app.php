<?php

require_once "vendor/autoload.php";

use App\Helpers\FileReader;
use App\Services\PaymentConversionService;
use App\Services\FileBasedCommissionsCalculatorService;
use App\Helpers\CountryCodeValidator;
use App\API\CurrencyProvider;
use App\API\LookupApiProvider;

try {
    $fileReader = new FileReader($argv[1]);
    $countryCodeValidator = new CountryCodeValidator;
    $lookupApiProvider = new LookupApiProvider;
    $currencyProvider = new CurrencyProvider;
    $paymentConversionService = new PaymentConversionService($countryCodeValidator, $lookupApiProvider, $currencyProvider);
    $FileBasedCommissionsCalculator = new FileBasedCommissionsCalculatorService($fileReader, $paymentConversionService);
    $FileBasedCommissionsCalculator->getCommissions();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

<?php

namespace App\Services;

use App\Helpers\FileReader;
use App\Helpers\CountryCodeValidator;
use App\API\CurrencyProvider;
use App\API\LookupApiProvider;


class FileBasedCommissionsCalculatorService
{
    private $apiKey;
    private $paymentConvertionService;

    public function __construct(private FileReader $fileReader) {

        $this->apiKey = getenv('EXCHANGERATES_API_KEY') ?? NULL;
        if (!$this->apiKey) {
            throw new \Exception("Invalid ENV value EXCHANGERATES_API_KEY!");
        }

        $cardDetailsProvider = new LookupApiProvider();
        $countryCodeValidator = new CountryCodeValidator();
        $currencyDataProvider = new CurrencyProvider($this->apiKey);

        $this->paymentConvertionService = new PaymentConversionService(
            $cardDetailsProvider,
            $countryCodeValidator,
            $currencyDataProvider,
        );
    }

    public function generateCommissions()
    {
        foreach ($this->fileReader->getLines() as $row) {
            $paymentData = json_decode($row);

            if (!isset($paymentData->bin)) {
                throw new \Exception("There is no bin value in file!");
            }

            $commissionAmountSummary = $this->paymentConvertionService->calculateTotalCommission($paymentData);
            echo "The commission for bin { $paymentData->bin } is: { $commissionAmountSummary }\n";
        }
    }
}
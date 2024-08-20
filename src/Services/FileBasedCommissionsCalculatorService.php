<?php

namespace App\Services;

use App\Helpers\FileReader;
use App\Services\PaymentConversionService;

class FileBasedCommissionsCalculatorService
{
    private $fileReader;
    private $paymentConvertionService;

    public function __construct(FileReader $fileReader, PaymentConversionService $paymentConversionService)
    {

        $this->fileReader = $fileReader;
        $this->paymentConvertionService = $paymentConversionService;
    }

    public function getCommissions()
    {
        $fileExtractedData = $this->getFilesData();
        
        foreach ($fileExtractedData as $paymentData) {
            $paymentData = json_decode($paymentData);
            $commissionAmountSummary = $this->paymentConvertionService->calculateTotalCommission($paymentData);
            echo "The commission for bin { $paymentData->bin } is: { $commissionAmountSummary }\n";
        }
    }

    private function getFilesData(): array
    {
        return $this->fileReader->getLines() ?? [];
    }
}

<?php

namespace App\Services;

use App\Helpers\FileReader;
use App\Services\PaymentConversionService;

class FileBasedCommissionsCalculatorService
{
    public function __construct(private FileReader $fileReader, private PaymentConversionService $paymentConversionService)
    {

        $this->fileReader = $fileReader;
        $this->paymentConversionService = $paymentConversionService;
    }

    public function getCommissions()
    {
        $fileExtractedData = $this->getFilesData();
        
        foreach ($fileExtractedData as $paymentData) {
            $paymentData = json_decode($paymentData);
            $commissionAmountSummary = $this->paymentConversionService->calculateTotalCommission($paymentData);
            echo "The commission for bin { $paymentData->bin } is: { $commissionAmountSummary }\n";
        }
    }

    private function getFilesData(): array
    {
        return $this->fileReader->getLines() ?? [];
    }
}

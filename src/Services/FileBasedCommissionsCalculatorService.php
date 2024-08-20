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
        foreach ($this->getFilesData() as $paymentData) {
            $paymentData = json_decode($paymentData);
            $commissionAmountSummary = $this->paymentConversionService->calculateTotalCommission($paymentData);
            echo "The commission for bin { $paymentData->bin } is: { $commissionAmountSummary }\n";
        }
    }

    private function getFilesData(): array|null
    {
        return $this->fileReader->getLines() ?? throw new \Exception('There is no proper data in file');
    }
}

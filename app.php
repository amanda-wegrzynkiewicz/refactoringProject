<?php

require_once "vendor/autoload.php";

use App\Helpers\FileReader;
use App\Services\FileBasedCommissionsCalculatorService;

$filePath = $argv[1];

$fileReader = new FileReader($filePath);
$FileBasedCommissionsCalculator = new FileBasedCommissionsCalculatorService($fileReader);
$FileBasedCommissionsCalculator->generateCommissions();

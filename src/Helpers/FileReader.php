<?php

namespace App\Helpers;

class FileReader
{
    public $allowedExtensions = ['txt', 'json'];

    public function __construct(private string $filePath)
    {
        $pathInfo = pathinfo($filePath);
        if (!in_array($pathInfo['extension'], $this->allowedExtensions)) {
            throw new \Exception("File extension not allowed.");
        }

        if (!file_exists($filePath)) {
            throw new \Exception("File not found: " . $pathInfo['extension']);
        }

        if (!is_readable($filePath)) {
            throw new \Exception("File cannot be read.");
        }

        $this->filePath = $filePath;
    }

    public function getLines(): array
    {
        return $this->filePath ? file($this->filePath) : [];
    }
}

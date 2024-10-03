<?php

namespace App\Service;

class BarcodeGeneratorService
{
    public function generate(string $code): string
    {
        $url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($code);

        // Fetch the QR code image
        $imageData = @file_get_contents($url);

        if ($imageData === false) {
            // Handle error, e.g., log it or throw an exception
            throw new \RuntimeException('Unable to fetch QR code image from API.');
        }

        return $imageData; // Return the PNG image as a string
    }
}

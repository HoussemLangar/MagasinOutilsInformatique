<?php

namespace App\Controller;

use App\Service\BarcodeGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BarcodeController extends AbstractController
{
    private BarcodeGeneratorService $barcodeGenerator;

    public function __construct(BarcodeGeneratorService $barcodeGenerator)
    {
        $this->barcodeGenerator = $barcodeGenerator;
    }

    /**
     * @Route("/barcode/generate/{code}", name="barcode_generate")
     */
    public function generateBarcode(string $code): Response
    {
        try {
            // Generate the QR code image
            $barcode = $this->barcodeGenerator->generate($code);

            return new Response(
                $barcode,
                200,
                ['Content-Type' => 'image/png']
            );
        } catch (\RuntimeException $e) {
            // Handle error, e.g., return a 500 Internal Server Error response
            return new Response(
                'Unable to generate barcode',
                500
            );
        }
    }
}


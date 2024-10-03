<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ErrorController extends AbstractController
{
    /**
     * @Route("/error/{code}", name="error")
     */
    public function showError(Request $request, $code): Response
    {
        $exception = $request->attributes->get('exception');
        $message = $exception ? $exception->getMessage() : 'An error occurred.';

        return $this->render('error.html.twig', [
            'code' => $code,
            'message' => $message,
        ]);
    }
}

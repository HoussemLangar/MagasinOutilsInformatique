<?php
namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtiquetteController extends AbstractController
{
    private $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/etiquettes", name="view_etiquettes")
     */
    public function viewEtiquettes(): Response
    {
        $articles = $this->articleRepository->findAll();

        return $this->render('outil/view.html.twig', [
            'articles' => $articles,
        ]);
    }
}
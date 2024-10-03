<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Repository\MagasinRepository;
use App\Repository\EmplacementRepository;
use App\Repository\EtiquetteRepository;
use App\Repository\RapportRepository;
use App\Repository\UtilisateurRepository;

class AdminController extends AbstractController
{
    private $articleRepository;
    private $magasinRepository;
    private $emplacementRepository;
    private $etiquetteRepository;
    private $rapportRepository;
    private $utilisateurRepository;

    public function __construct(
        ArticleRepository $articleRepository,
        MagasinRepository $magasinRepository,
        EmplacementRepository $emplacementRepository,
        EtiquetteRepository $etiquetteRepository,
        RapportRepository $rapportRepository,
        UtilisateurRepository $utilisateurRepository
    ) {
        $this->articleRepository = $articleRepository;
        $this->magasinRepository = $magasinRepository;
        $this->emplacementRepository = $emplacementRepository;
        $this->etiquetteRepository = $etiquetteRepository;
        $this->rapportRepository = $rapportRepository;
        $this->utilisateurRepository = $utilisateurRepository;
    }

    /**
     * @Route("/dashboard", name="admin_dashboard")
     */
    public function index(): Response
    {
        $reports = $this->rapportRepository->findAll();
        return $this->render('admin.html.twig', [
            'articles' => $this->articleRepository->findAll(),
            'magasins' => $this->magasinRepository->findAll(),
            'emplacements' => $this->emplacementRepository->findAll(),
            'etiquettes' => $this->etiquetteRepository->findAll(),
            'rapports' => $reports,
            'utilisateur' => $this->getUser(),
            'utilisateurs' => $this->utilisateurRepository->findAll(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\EmplacementRepository;
use App\Repository\EtiquetteRepository;
use App\Repository\MagasinRepository;
use App\Repository\RapportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class UserController extends AbstractController
{
    private TokenStorageInterface $tokenStorage;
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @Route("/dashboard", name="user_dashboard")
     */
    public function index(
        ArticleRepository $articleRepository,
        EmplacementRepository $emplacementRepository,
        EtiquetteRepository $etiquetteRepository,
        MagasinRepository $magasinRepository,
        RapportRepository $rapportRepository,
        
    ): Response {
        // Récupérer les données nécessaires
        $articles = $articleRepository->findAll();
        $emplacements = $emplacementRepository->findAll();
        $etiquettes = $etiquetteRepository->findAll();
        $magasins = $magasinRepository->findAll();
        $rapports = $rapportRepository->findAll();

        // Récupérer l'utilisateur courant
        $user = $this->tokenStorage->getToken()?->getUser();
        if (!$user || !$this->authorizationChecker->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Access denied.');
        }

        return $this->render('Utilisateur.html.twig', [
            'articles' => $articles,
            'emplacements' => $emplacements,
            'etiquettes' => $etiquettes,
            'magasins' => $magasins,
            'rapports' => $rapports,
            'utilisateur' => $user
            ]);
    }
}

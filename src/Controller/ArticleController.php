<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Etiquette;
use App\Form\ArticleType;
use App\Service\BarcodeGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    private BarcodeGeneratorService $barcodeGenerator;
    private EntityManagerInterface $entityManager;

    public function __construct(BarcodeGeneratorService $barcodeGenerator, EntityManagerInterface $entityManager)
    {
        $this->barcodeGenerator = $barcodeGenerator;
        $this->entityManager = $entityManager;
    }

    #[Route('/articles/voir', name: 'voir_articles')]
    public function view(Request $request): Response
    {
        $query = $request->query->get('query');
        $articles = $this->entityManager->getRepository(Article::class)->searchByReference($query);

        return $this->render('article/view.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/articles/ajouter', name: 'ajouter_article')]
    public function add(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si l'article existe déjà
            $existingArticle = $this->entityManager->getRepository(Article::class)
                ->findOneBy(['reference' => $article->getReference()]);

            if ($existingArticle) {
                $this->addFlash('error', 'Un article avec cette référence existe déjà.');
                return $this->render('article/add.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Générer le code barre
            $codeBarre = $this->barcodeGenerator->generate($article->getReference());

            $etiquette = new Etiquette();
            $etiquette->setContenu($codeBarre);
            $etiquette->setArticle($article);

            $this->entityManager->persist($article);
            $this->entityManager->persist($etiquette);

            try {
                $this->entityManager->flush();
                $this->addFlash('success', 'Article ajouté avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout de l\'article : ' . $e->getMessage());
            }

            return $this->redirectToRoute('voir_articles');
        }

        return $this->render('article/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/articles/modifier/{id}', name: 'modifier_article')]
    public function edit(Request $request, int $id): Response
    {
        $article = $this->entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->flush();
                $this->addFlash('success', 'Article modifié avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la modification de l\'article : ' . $e->getMessage());
            }

            return $this->redirectToRoute('voir_articles');
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/articles/supprimer/{id}', name: 'supprimer_article')]
    public function delete(int $id): Response
    {
        $article = $this->entityManager->getRepository(Article::class)->find($id);

        if ($article) {
            try {
                $this->entityManager->remove($article);
                $this->entityManager->flush();
                $this->addFlash('success', 'Article supprimé avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression de l\'article : ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Article non trouvé.');
        }

        return $this->redirectToRoute('voir_articles');
    }
}

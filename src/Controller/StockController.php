<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Operation;
use App\Repository\ArticleRepository;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\ORM\Exception\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Mailer\MailerInterface;
use App\Service\NotificationService;

class StockController extends AbstractController
{
    private $entityManager;
    private $operationRepository;
    private $articleRepository;
    private $mailer;
    private $notificationService;

    public function __construct(
        EntityManagerInterface $entityManager, 
        OperationRepository $operationRepository, 
        ArticleRepository $articleRepository, 
        MailerInterface $mailer, 
        NotificationService $notificationService
    ) {
        $this->entityManager = $entityManager;
        $this->operationRepository = $operationRepository;
        $this->articleRepository = $articleRepository;
        $this->mailer = $mailer;
        $this->notificationService = $notificationService;
    }

    /**
     * @Route("/stock/operations", name="stock_operations")
     */
    public function viewOperations(Request $request): Response
    {
        try {
            $search = $request->query->get('search');
            $operations = $search ? 
                $this->operationRepository->findBySearchTerm($search) : 
                $this->operationRepository->findAll();
    
            $lowStockArticles = $this->articleRepository->findLowStockArticles();
            
            foreach ($lowStockArticles as $article) {
                try {
                    $this->notificationService->sendLowStockAlert($article);
                    $this->addFlash('success', 'Email notification sent for low stock article: ' . $article->getReference());
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Failed to send email notification for article: ' . $article->getReference() . '. Error: ' . $e->getMessage());
                }
            }
    
            return $this->render('stock/operations.html.twig', [
                'operations' => $operations,
                'lowStockArticles' => $lowStockArticles,
                'utilisateur' => $this->getUser(), 
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Impossible de récupérer les opérations : ' . $e->getMessage());
            return $this->render('stock/operations.html.twig', [
                'operations' => [],
                'lowStockArticles' => [],
                'utilisateur' => null, 
            ]);
        }
    }
    

    /**
     * @Route("/stock/operation/add", name="add_operation")
     */
    public function addOperation(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            try {
                $articleId = $request->request->get('article_id');
                $quantity = (int) $request->request->get('quantity');
                $type = $request->request->get('type'); 

                $article = $this->articleRepository->find($articleId);
                if (!$article) {
                    $this->addFlash('error', 'Article not found.');
                    return $this->redirectToRoute('stock_operations');
                }

                if ($type === 'add') {
                    $article->setQuantite($article->getQuantite() + $quantity);
                } elseif ($type === 'subtract') {
                    $newQuantity = $article->getQuantite() - $quantity;
                    if ($newQuantity < 0) {
                        $this->addFlash('error', 'La quantité ne peut pas être négative.');
                        return $this->redirectToRoute('stock_operations');
                    }
                    $article->setQuantite($newQuantity);
                }

                
                $lastOperation = $this->operationRepository->findOneBy([], ['numOperation' => 'DESC']);
                $numOperation = $lastOperation ? $lastOperation->getNumOperation() + 1 : 1;
                $operation = new Operation();
                $operation->setNumOperation($numOperation);
                $operation->setArticle($article);
                $operation->setQuantite($quantity);
                $operation->setType($type);
                $operation->setDateSortie(new \DateTime());

                $this->entityManager->persist($operation);
                $this->entityManager->flush();

                $this->addFlash('success', 'Operation added successfully.');
                return $this->redirectToRoute('stock_operations'); 

            } catch (\Exception $e) {
                $this->addFlash('error', 'An unexpected error occurred: ' . $e->getMessage());
                return $this->redirectToRoute('stock_operations');
            }
        }

        return $this->render('stock/add.html.twig', [
            'articles' => $this->articleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/stock/operation/edit/{id}", name="edit_operation")
     */
    public function editOperation(int $id, Request $request): Response
    {
        try {
            $operation = $this->operationRepository->find($id);
            if (!$operation) {
                throw $this->createNotFoundException('Operation not found');
            }

            if ($request->isMethod('POST')) {
                $quantity = (int) $request->request->get('quantity');
                $operation->setQuantite($quantity);

                $this->entityManager->flush();

                $this->addFlash('success', 'Operation updated successfully.');
                return $this->redirectToRoute('stock_operations');
            }

            return $this->render('stock/edit.html.twig', [
                'operation' => $operation,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to update operation: ' . $e->getMessage());
            return $this->redirectToRoute('stock_operations');
        }
    }

    /**
     * @Route("/stock/operation/{id}/delete", name="delete_operation", methods={"POST"})
     */
    public function delete(int $id, Request $request): RedirectResponse
    {
        try {
            // Ensure CSRF protection
            $csrfToken = $request->request->get('_token');
            if (!$this->isCsrfTokenValid('delete' . $id, $csrfToken)) {
                throw new AccessDeniedException('Invalid CSRF token.');
            }

            $operation = $this->operationRepository->find($id);
            if (!$operation) {
                throw $this->createNotFoundException('Operation not found');
            }

            $this->entityManager->remove($operation);
            $this->entityManager->flush();

            $this->addFlash('success', 'Operation deleted successfully');

        } catch (OptimisticLockException | ORMException $e) {
            $this->addFlash('error', 'Failed to delete operation: ' . $e->getMessage());
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', 'An unexpected error occurred: ' . $e->getMessage());
        }

        return $this->redirectToRoute('stock_operations');
    }
}

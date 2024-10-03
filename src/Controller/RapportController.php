<?php

namespace App\Controller;

use App\Form\RapportType;
use App\Entity\Rapport;
use App\Repository\OperationRepository;
use App\Repository\RapportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface; 
use Dompdf\Dompdf;
use Dompdf\Options;

class RapportController extends AbstractController
{
    private $logger;
    private $entityManager;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    #[Route('/reports/generate', name: 'generate_report')]
    public function generateRapport(Request $request, OperationRepository $operationRepository): Response
    {
        $form = $this->createForm(RapportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = $form->get('date')->getData();
            $operationTypes = $form->get('operationType')->getData();
            $articles = $form->get('article')->getData();

            $articleArray = $articles instanceof \Doctrine\Common\Collections\Collection ? $articles->toArray() : [];

            $this->logger->info('Date: ' . $date->format('Y-m-d'));
            $this->logger->info('Operation Type: ' . $operationTypes);
            $this->logger->info('Articles: ' . json_encode($articleArray));

            $operations = $operationRepository->findByCriteria($date, $operationTypes, $articleArray);

            $this->logger->info('Number of operations found: ' . count($operations));

            if (count($operations) > 0) {
                $rapport = new Rapport();
                $rapport->setDateJour($date);
                $rapport->setQuantite(array_sum(array_map(fn($op) => $op->getQuantite(), $operations)));

                foreach ($articles as $article) {
                    $rapport->addArticle($article);
                }

                foreach ($operations as $operation) {
                    $rapport->addOperation($operation);
                }

                $this->entityManager->persist($rapport);
                $this->entityManager->flush();

                return $this->redirectToRoute('report_list');
            } else {
                $this->addFlash('warning', 'Aucune opération trouvée pour les critères sélectionnés.');
            }
        }

        return $this->render('rapport/generate.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reports', name: 'report_list')]
    public function listRapports(RapportRepository $rapportRepository): Response
    {
        $rapports = $rapportRepository->findAll();

        return $this->render('rapport/list.html.twig', [
            'rapports' => $rapports,
        ]);
    }

    #[Route('/reports/export/{id}', name: 'export_report')]
    public function exportRapport(int $id, RapportRepository $rapportRepository): Response
    {
        $rapport = $rapportRepository->find($id);

        if (!$rapport) {
            throw $this->createNotFoundException('Rapport non trouvé.');
        }

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $html = $this->renderView('rapport/export.html.twig', [
            'rapport' => $rapport,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();

        return new Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="rapport_'.$rapport->getId().'.pdf"',
        ]);
    }

    #[Route('/reports/delete/{id}', name: 'delete_report')]
    public function deleteRapport(int $id, RapportRepository $rapportRepository): Response
    {
        $rapport = $rapportRepository->find($id);

        if (!$rapport) {
            throw $this->createNotFoundException('Rapport non trouvé.');
        }

        $this->entityManager->remove($rapport);
        $this->entityManager->flush();

        return $this->redirectToRoute('report_list');
    }
}

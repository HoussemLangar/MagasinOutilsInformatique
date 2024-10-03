<?php

namespace App\Controller;

use App\Entity\Emplacement;
use App\Form\EmplacementType;
use App\Repository\EmplacementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmplacementController extends AbstractController
{
    /**
     * @Route("/emplacements", name="emplacements_voir")
     */
    public function index(Request $request, EmplacementRepository $emplacementRepository): Response
    {
        $query = $request->query->get('query');

        if ($query) {
            $emplacements = $emplacementRepository->findByDescription($query);
        } else {
            $emplacements = $emplacementRepository->findAll();
        }

        return $this->render('emplacement/voir.html.twig', [
            'emplacements' => $emplacements,
        ]);
    }

    /**
     * @Route("/emplacement/ajouter", name="emplacement_ajouter")
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $emplacement = new Emplacement();
        $form = $this->createForm(EmplacementType::class, $emplacement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($emplacement);
            $entityManager->flush();

            $this->addFlash('success', 'Emplacement ajouté avec succès.');

            return $this->redirectToRoute('emplacements_voir');
        }

        return $this->render('emplacement/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/emplacement/{id}/modifier", name="emplacement_modifier")
     */
    public function edit(Request $request, Emplacement $emplacement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmplacementType::class, $emplacement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Emplacement modifié avec succès.');

            return $this->redirectToRoute('emplacements_voir');
        }

        return $this->render('emplacement/modifier.html.twig', [
            'form' => $form->createView(),
            'emplacement' => $emplacement,
        ]);
    }

    /**
     * @Route("/emplacement/{id}/supprimer", name="emplacement_supprimer", methods={"POST"})
     */
    public function delete(Request $request, Emplacement $emplacement, EntityManagerInterface $entityManager): Response
    {
            try {
                $entityManager->remove($emplacement);
                $entityManager->flush();

                $this->addFlash('success', 'Emplacement supprimé avec succès.');
            } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $e) {
                $this->addFlash('error', 'Erreur lors de la suppression de l\'emplacement : ' . $e->getMessage());
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur : ' . $e->getMessage());
            }
        

        return $this->redirectToRoute('emplacement_voir');
    }
}

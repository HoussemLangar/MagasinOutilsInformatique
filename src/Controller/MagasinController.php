<?php

namespace App\Controller;

use App\Entity\Magasin;
use App\Form\MagasinType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MagasinController extends AbstractController
{
    /**
     * @Route("/magasins", name="magasins_voir")
     */
    public function voirMagasins(Request $request, EntityManagerInterface $em): Response
    {
        $query = $request->query->get('query');

    if ($query) {
        $magasins = $em->getRepository(Magasin::class)->findByName($query);
    } else {
        $magasins = $em->getRepository(Magasin::class)->findAll();
    }

        return $this->render('magasin/voir.html.twig', [
            'magasins' => $magasins,
        ]);
    }

    /**
     * @Route("/magasin/ajouter", name="magasin_ajouter")
     */
    public function ajouterMagasin(Request $request, EntityManagerInterface $em): Response
    {
        $magasin = new Magasin();
        $form = $this->createForm(MagasinType::class, $magasin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($magasin);
            $em->flush();

            $this->addFlash('success', 'Magasin ajouté avec succès.');

            return $this->redirectToRoute('voir_magasins');
        }

        return $this->render('magasin/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/magasin/modifier/{id}", name="modifier_magasin")
     */
    public function modifierMagasin(Magasin $magasin, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MagasinType::class, $magasin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Magasin modifié avec succès.');

            return $this->redirectToRoute('voir_magasins');
        }

        return $this->render('magasin/modifier.html.twig', [
            'form' => $form->createView(),
            'magasin' => $magasin,
        ]);
    }

    /**
     * @Route("/magasin/supprimer/{id}", name="supprimer_magasin")
     */
    public function supprimerMagasin(Magasin $magasin, EntityManagerInterface $em): Response
{
    try {
        $em->remove($magasin);
        $em->flush();
        $this->addFlash('success', 'Magasin supprimé avec succès.');
    } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $e) {
        $this->addFlash('error', 'Erreur lors de la suppression du magasin : ' . $e->getMessage());
    } catch (\Exception $e) {
        $this->addFlash('error', 'Erreur : ' . $e->getMessage());
    }
    return $this->redirectToRoute('magasins_voir');
}

}

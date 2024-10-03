<?php
namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Role;
use App\Repository\RoleRepository;
use App\Form\RoleType;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManagementController extends AbstractController
{
    private $entityManager;
    private $roleRepository;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, RoleRepository $roleRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->roleRepository = $roleRepository;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @Route("/utilisateurs", name="voir_utilisateurs")
     */
    public function voirUtilisateurs(Request $request, UtilisateurRepository $utilisateurRepository): Response
    {
        $query = $request->query->get('query');
        $utilisateurs = $query ? $utilisateurRepository->findBySearchQuery($query) : $utilisateurRepository->findAll();

        $roles = $this->roleRepository->findAll();

        return $this->render('users/view.html.twig', [
            'utilisateurs' => $utilisateurs,
            'roles' => $roles, 
        ]);
    }

    /**
     * @Route("/utilisateur/creer", name="creer_utilisateur")
     */
    public function creerUtilisateur(Request $request): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password
            $motDePasse = $form->get('motDePasse')->getData();
            if ($motDePasse) {
                $utilisateur->setMotDePasse($this->passwordHasher->hashPassword($utilisateur, $motDePasse));
            }

            $roles = $form->get('role')->getData();
            foreach ($roles as $role) {
                $roleEntity = $this->roleRepository->find($role); 
                $utilisateur->addRole($roleEntity);
            }

            $this->entityManager->persist($utilisateur);
            $this->entityManager->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès.');

            return $this->redirectToRoute('voir_utilisateurs');
        }

        return $this->render('users/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/utilisateur/{id}/modifier", name="modifier_utilisateur")
     */
    public function modifierUtilisateur(Request $request, Utilisateur $utilisateur): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur , [
            'is_edit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $motDePasse = $form->get('motDePasse')->getData();
            if ($motDePasse) {
                $utilisateur->setMotDePasse($this->passwordHasher->hashPassword($utilisateur, $motDePasse));
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès.');

            return $this->redirectToRoute('voir_utilisateurs');
        }

        return $this->render('users/modifier.html.twig', [
            'form' => $form->createView(),
            'utilisateur' => $utilisateur, 
        ]);
    }

    /**
     * @Route("/utilisateur/{id}/supprimer", name="supprimer_utilisateur")
     */
    public function supprimerUtilisateur(Utilisateur $utilisateur): Response
    {
        $this->entityManager->remove($utilisateur);
        $this->entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('voir_utilisateurs');
    }
}
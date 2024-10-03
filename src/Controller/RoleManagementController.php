<?php

namespace App\Controller;

use App\Entity\Role;
use App\Form\RoleType;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoleManagementController extends AbstractController
{
    private $entityManager;
    private $roleRepository;

    public function __construct(EntityManagerInterface $entityManager, RoleRepository $roleRepository)
    {
        $this->entityManager = $entityManager;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @Route("/roles", name="roles_list")
     */
    public function listRoles(): Response
    {
        $roles = $this->roleRepository->findAll();

        return $this->render('roles/list.html.twig', [
            'roles' => $roles,
        ]);
    }

    /**
     * @Route("/roles/new", name="new_role")
     */
    public function createRole(Request $request): Response
    {
        $role = new Role();
        $form = $this->createForm(RoleType::class, $role);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($role);
            $this->entityManager->flush();

            $this->addFlash('success', 'Role added successfully.');

            return $this->redirectToRoute('roles_list');
        }

        return $this->render('roles/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/roles/{id}/edit", name="edit_role")
     */
    public function editRole(Request $request, Role $role): Response
    {
        $form = $this->createForm(RoleType::class, $role);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Role updated successfully.');

            return $this->redirectToRoute('roles_list');
        }

        return $this->render('roles/edit.html.twig', [
            'form' => $form->createView(),
            'role' => $role,
        ]);
    }

    /**
     * @Route("/roles/{id}/delete", name="delete_role")
     */
    public function deleteRole(Role $role): Response
    {
        if ($role->getNom() !== 'admin') {
            $this->entityManager->remove($role);
            $this->entityManager->flush();

            $this->addFlash('success', 'Role deleted successfully.');
        } else {
            $this->addFlash('error', 'Cannot delete the admin role.');
        }

        return $this->redirectToRoute('roles_list');
    }
}

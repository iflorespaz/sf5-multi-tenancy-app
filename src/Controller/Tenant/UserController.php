<?php

namespace App\Controller\Tenant;

use App\Entity\Tenant\User;
use App\Form\Tenant\UserType;
use App\Repository\Tenant\UserRepository;
use App\Services\SwitchTenantManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users")
 */
class UserController extends AbstractController
{
    public function __construct(SwitchTenantManager $switchTenantManager)
    {
        // This is only for testing, you should try to connect to tenant in the login process
        $switchTenantManager->reconnect();
    }

    /**
     * @Route("/", name="tenant_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {

        return $this->render('tenant/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="tenant_user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager('tenant');
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('tenant_user_index');
        }

        return $this->render('tenant/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tenant_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('tenant/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tenant_user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager('tenant')->flush();

            return $this->redirectToRoute('tenant_user_index');
        }

        return $this->render('tenant/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tenant_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager('tenant');
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tenant_user_index');
    }
}

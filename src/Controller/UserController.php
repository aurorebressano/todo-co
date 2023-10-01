<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Security\UserVoter;

class UserController extends AbstractController
{
    #[Route("/users", name: "user_list", methods: ['GET'])]
    public function list(UserRepository $userRepository)
    {
        $this->denyAccessUnlessGranted(UserVoter::VIEW, $user);
        return $this->render('user/list.html.twig', ['users' => $userRepository->findAll()]);
    }

    #[Route("/users/create", name: "user_create", methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->denyAccessUnlessGranted(UserVoter::CREATE, $this->getUser());
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($userPasswordHasherInterface->hashPassword($user, $form->get('password')->getData()));

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/users/{id \d+}/edit", name: "user_edit", methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request)
    {
        $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

    #[Route("/users/delete/{id}", name: "user_delete", methods: ['POST'])]
    public function delete(User $user, Request $request, EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->denyAccessUnlessGranted(UserVoter::DELETE, $user);
        $userAnonyme = $userRepository->findOneByUsername('anonyme');
        $tasksToReassign = $user->getTasks();

        foreach($tasksToReassign as $task)
        {
            $task->setUser($userAnonyme);
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', "L'utilisateur a bien été supprimé");

        return $this->redirectToRoute('user_list');
    }
}

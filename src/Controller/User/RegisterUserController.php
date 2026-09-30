<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Form\User\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
#[Route(path: '/inscription', name: 'inscription_user', methods: ['GET', 'POST'])]
class RegisterUserController
{
    public function __invoke(
        Environment $twig,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        FormFactoryInterface $formFactory,
    ): Response {
        $user = new User();
        $form = $formFactory->create(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            return new RedirectResponse('/');
        }

        return new Response($twig->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}

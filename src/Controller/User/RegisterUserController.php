<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\User\UserType;
use App\Repository\UserRepository;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
#[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
class RegisterUserController
{
    public function __invoke(Environment $twig, Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, FormFactoryInterface $formFactory): Response
    {
        $user = new User();
        $form = $formFactory->create(UserType::class, $user);
        $form->handleRequest($request);
        try {
            if($form->isSubmitted() && $form->isValid()){
                $hashedPassword = $userPasswordHasher->hashPassword($user, $form->get('password')->getData());
                $user->setPassword($hashedPassword);

                $user = $form->getData();
                $userRepository->persistAndSave($user);
            }
        } catch (LogicException $exception){

        }
        return new Response($twig->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]), Response::HTTP_OK);
    }
}

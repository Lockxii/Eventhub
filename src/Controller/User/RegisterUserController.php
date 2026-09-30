<?php

namespace App\Controller\User;


use App\Entity\User;
use App\Form\User\UserType;
use App\Repository\UserRepository;
use http\Env\Response;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[AsController]
#[Route(path: '/inscription', name: 'inscription_user',methods: ['GET,POST'])]
class RegisterUserController
{
    public function __invoke(Environment $twig, Request $request, UserRepository $userRepository, UserPasswordHasherInterface
     $passwordHasher, FormFactoryInterface $formFactory): Response
    {
        $user = new User();
        $form =  $formFactory->create(UserType::class, $user);

        return new Response($twig->render(''))
    }
}

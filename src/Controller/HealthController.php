<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Attribute\AsController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/health-check', name: 'health_check', methods: ['GET'])]
final class HealthController
{
    public function __invoke(): Response
    {
        return new JsonResponse('OK',JsonResponse::HTTP_OK,[],true);
    }
}

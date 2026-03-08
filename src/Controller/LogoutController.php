<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LogoutController extends AbstractController
{
    #[Route('/logout', name: 'app_logout', methods: ['DELETE'])]
    public function index(Security $security): Response
    {
        $security->logout();
        return $this->redirectToRoute('app_login');
    }
}

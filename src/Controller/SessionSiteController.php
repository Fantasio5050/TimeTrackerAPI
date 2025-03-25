<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SessionSiteController extends AbstractController
{
    #[Route('/session/site', name: 'app_session_site')]
    public function index(): Response
    {
        return $this->render('session_site/index.html.twig', [
            'controller_name' => 'SessionSiteController',
        ]);
    }
}

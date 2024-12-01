<?php

namespace App\Controller\Client;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/client')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'client_dashboard')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        return $this->render('Client/index.html.twig');
    }
}
<?php

namespace App\Controller\frontEnd;

use App\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/client', name: 'client')]
class ClientController extends AbstractController
{
    #[Route('/{name}', name: '_show')]
    public function showClient(Client $client): Response
    {
        return $this->render('front-end/client/show.html.twig', [
            'clients' => $client,
        ]);
    }
}
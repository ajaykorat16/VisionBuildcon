<?php

namespace App\Controller\frontEnd;

use App\Repository\ClientRepository;
use App\Repository\TeamsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/about', name: 'about')]
class AboutController extends AbstractController
{
    public function __construct(
        private readonly TeamsRepository $teamsRepository,
        private readonly ClientRepository $clientRepository
    )
    {
    }

    #[Route('', name: '_index')]
    public function index(): Response
    {
        return $this->render('front-end/aboutUs/about.html.twig',[
            'team' => $this->teamsRepository->findActiveTeams(),
            'client' => $this->clientRepository->findActiveClients()
        ]);
    }
}
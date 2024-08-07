<?php

namespace App\Controller\FrontEnd;

use App\Repository\TeamsRepository;
use App\Repository\ServicesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/about', name: 'about')]
class AboutController extends AbstractController
{
    public function __construct(
        private readonly TeamsRepository $teamsRepository,
        private readonly ServicesRepository $servicesRepository
    ){
    }

    #[Route('', name: '_index')]
    public function index(): Response
    {
        return $this->render('front-end/aboutUs/about.html.twig',[
            'team' => $this->teamsRepository->getActiveTeams(),
            'service' => $this->servicesRepository->getServices()
        ]);
    }
}
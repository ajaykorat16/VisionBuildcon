<?php

namespace App\Controller\FrontEnd;

use App\Entity\Request as RequestEntity;
use App\Entity\Services;
use App\Form\RequestType;
use App\Repository\ClientRepository;
use App\Repository\ProjectRepository;
use App\Repository\ServicesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/services', name: 'services')]
class ServicesController extends AbstractController
{
    public function __construct(
        private readonly ServicesRepository $servicesRepository,
        private readonly ClientRepository $clientRepository,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/', name: '_index')]
    public function index(Request $request): Response
    {
        $requestEntity = new RequestEntity();

        $form = $this->createForm(RequestType::class, $requestEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($requestEntity);
            $this->entityManager->flush();

            $this->addFlash('success', 'Request added successfully.');
            return $this->redirectToRoute('services_index');
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('front-end/services/services.html.twig',[
                'service' => $this->servicesRepository->findActiveServices(),
                'client' => $this->clientRepository->findActiveClients(),
                'form' => $form->createView()
            ]);
        }

        return $this->render('front-end/services/services.html.twig',[
            'service' => $this->servicesRepository->findActiveServices(),
            'client' => $this->clientRepository->findActiveClients(),
            'form' => $form->createView()
        ]);
    }

    #[Route('/{name}', name: '_show_service')]
    public function showService(Services $services): Response
    {
        return $this->render('front-end/services/show.html.twig', [
            'services' => $services,
        ]);
    }
}
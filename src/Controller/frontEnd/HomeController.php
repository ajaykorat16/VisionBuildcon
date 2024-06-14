<?php

namespace App\Controller\frontEnd;

use App\Entity\Request as RequestEntity;
use App\Form\RequestType;
use App\Repository\ProjectRepository;
use App\Repository\ServicesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('', name: 'home')]
class HomeController extends AbstractController
{
    public function __construct(
        private readonly ServicesRepository $servicesRepository,
        private readonly ProjectRepository $projectRepository,
        private readOnly EntityManagerInterface $entityManager
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
            return $this->redirectToRoute('home_index');
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('front-end/home/content.html.twig', [
                'service' => $this->servicesRepository->findActiveServices(),
                'project' => $this->projectRepository->findActiveProjects(),
                'form' => $form->createView()
            ]);
        }

        return $this->render('front-end/home/content.html.twig', [
            'service' => $this->servicesRepository->findActiveServices(),
            'project' => $this->projectRepository->findActiveProjects(),
            'form' => $form->createView()
        ]);
    }
}
<?php

namespace App\Controller\FrontEnd;

use App\Entity\Request as RequestEntity;
use App\Form\RequestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contact', name: 'contact_us')]
class ContactUsController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ){
    }

    #[Route('', name: '_index')]
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

        return $this->render('front-end/aboutUs/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
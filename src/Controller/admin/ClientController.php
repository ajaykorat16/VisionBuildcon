<?php

namespace App\Controller\admin;
use App\Entity\Client;
use App\Form\ClientType;
use App\Form\SearchType;
use App\Repository\ClientRepository;
use App\Uploader\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin/clients',name: 'clients')]
class ClientController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ClientRepository $clientRepository
    )
    {
    }

    #[Route('/', name: '_list')]
    public function index(Request $request):Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $search = $form->get('search')->getData();

        $clients = $this->clientRepository->findAllActiveClients(
            0,
            clientRepository::PAGE_SIZE,
            $search
        );

        return $this->render('admin/client/index.html.twig',[
            'client' => $clients,
            'totalProjects' => $this->clientRepository->findAllActiveClientsCount(),
            'search' => $form->createView()
        ]);
    }

    #[Route('/load-more', name: '_load_more', options: ['expose' => true])]
    public function loadMore(Request $request): Response
    {
        $offset = (int) $request->query->get('offset');
        $clients = $this->clientRepository->findAllActiveClients($offset, ClientRepository::PAGE_SIZE);
        $content = '';

        foreach ($clients as $client) {
            $content .= $this->renderView('admin/client/list-items.html.twig', ['client' => $client]);
        }

        return new Response($content);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $client->setLogo($form->get('logo')->getData());

            $this->entityManager->persist($client);
            $this->entityManager->flush();

            $this->addFlash('success', 'New client has been created successfully.');
            return $this->redirectToRoute('clients_list');
        }

        return $this->render('admin/client/create.html.twig', [
            'form' => $form->createView(),
            'client' => $client,
        ]);
    }

    #[Route('/edit/{id}', name: '_edit')]
    public function edit(Request $request, ImageUploader $imageUploader, Client $client):Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('logo')->getData();
            $client->setLogo($imageFile);

            $this->entityManager->flush();

            $this->addFlash('success','New client has updated Successfully...');
            return $this->redirectToRoute('clients_list');
        }

        return $this->render('admin/client/create.html.twig',[
            'form' => $form->createView(),
            'client' => $client
        ]);
    }

    #[Route('/show/{id}', name: '_show')]
    public function show(Client $client): Response
    {
        return $this->render('admin/client/view.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/delete/{id}', name: '_delete', options: ['expose' => true])]
    public function delete(Client $client): JsonResponse
    {
        $client->setDeletedAt(new \DateTime());
        $this->entityManager->flush();

        $this->addFlash('success', 'Client has been deleted.');

        return new JsonResponse(['status' => 'ok', 'message' => 'Client has been deleted.']);
    }
}
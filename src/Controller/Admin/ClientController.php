<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use App\Form\ClientType;
use App\Form\SearchType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin/clients', name: 'clients')]
class ClientController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
        private readonly ClientRepository $clientRepository
    ){
    }

    #[Route('/', name: '_list')]
    public function index(Request $request):Response
    {
        $searchForm = $this->createForm(SearchType::class);
        $searchForm->handleRequest($request);

        $searchData = $searchForm->get('search')->getData();

        $clients = $this->clientRepository->getActiveClients(
            $searchData,
            clientRepository::PAGE_SIZE,
            ClientRepository::OFFSET
        );

        return $this->render('admin/client/index.html.twig',[
            'client' => $clients,
            'totalClients' => $this->clientRepository->getTotalCountClients(),
            'search' => $searchForm->createView()
        ]);
    }

    #[Route('/load-more', name: '_load_more', options: ['expose' => true])]
    public function loadMore(Request $request): JsonResponse
    {
        $offset = (int) $request->query->get('offset');
        $clients = $this->clientRepository->getActiveClients(null, ClientRepository::PAGE_SIZE, $offset);
        $content = [];

        foreach ($clients as $client) {
            $content[] = $this->renderView('admin/client/list-items.html.twig', ['clients' => $client]);
        }

        return new JsonResponse(['content' => $content], JsonResponse::HTTP_OK);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($client);
            $this->entityManager->flush();

            $this->addFlash('success', 'New client has been created successfully.');

            return $this->redirectToRoute('clients_list');
        }

        return $this->render('admin/client/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: '_edit')]
    public function edit(Request $request, Client $client): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $old_image = $request->request->get('old_photo');
            $new_image = $form->get('logo')->getData();

            if ($new_image) {
                if($old_image != $new_image)
                {
                    $filePath = 'image/' . $old_image;

                    if (!empty($old_image) && file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                $client->setLogo($new_image);

            }elseif($old_image){  

                $client->setLogo($old_image);  
            }

            $this->entityManager->flush();

            $this->addFlash('success', sprintf('Client %d has been updated successfully.', $client->getId()));
            
            return $this->redirectToRoute('clients_list');
        }

        return $this->render('admin/client/create.html.twig', [
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

    #[Route('/delete/{id}', name: '_delete')]
    public function delete(Client $client): Response
    {
        $client->setDeletedAt(new \DateTime());
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Client %s has been deleted successfully.', $client->getName()));

        return $this->redirectToRoute('clients_list');
    }
}
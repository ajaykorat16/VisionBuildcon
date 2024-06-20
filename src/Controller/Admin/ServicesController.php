<?php

namespace App\Controller\Admin;

use App\Entity\Services;
use App\Form\SearchType;
use App\Form\ServiceType;
use App\Repository\ServicesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin/services', name: 'services')]
class ServicesController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ServicesRepository $servicesRepository
    )
    {
    }

    #[Route('/', name: '_list')]
    public function index(Request $request):Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $search = $form->get('search')->getData();

        $services = $this->servicesRepository->findAllActiveServices(
            0,
            ServicesRepository::PAGE_SIZE,
            $search
        );

        return $this->render('admin/services/index.html.twig',[
            'services' => $services,
            'totalServices' => $this->servicesRepository->findAllActiveServicesCount(),
            'search' => $form->createView()
        ]);
    }

    #[Route('/load-more', name: '_load_more', options: ['expose' => true])]
    public function loadMore(Request $request): Response
    {
        $offset = (int) $request->query->get('offset');
        $services = $this->servicesRepository->findAllActiveServices($offset, ServicesRepository::PAGE_SIZE);
        $content = '';

        foreach ($services as $service) {
            $content .= $this->renderView('admin/project/list-items.html.twig', ['service' => $service]);
        }

        return new Response($content);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request):Response
    {
        $service = new Services();

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($service);
            $this->entityManager->flush();

            $this->addFlash('success','New Service has Created Successfully...');
            return $this->redirectToRoute('services_list');
        }

        return $this->render('admin/services/create.html.twig',[
            'form' => $form->createView(),
            'service' => $service
        ]);
    }

    #[Route('/edit/{id}', name: '_edit')]
    public function edit(Request $request, Services $service):Response
    {
        if (file_exists($service->getServicePhoto())) {
            unlink('image/' .$service->getServicePhoto() );
            $service->setServicePhoto(null);
        }

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();

            $this->addFlash('success','Service has been updated Successfully...');
            return $this->redirectToRoute('services_list');
        }

        return $this->render('admin/services/create.html.twig',[
            'form' => $form->createView(),
            'service' => $service
        ]);
    }

    #[Route('/show/{id}', name: '_show')]
    public function show(Services $service): Response
    {
        return $this->render('admin/services/view.html.twig', [
            'services' => $service,
        ]);
    }

    #[Route('/delete/{id}', name: '_delete' ,  options: ['expose' => true])]
    public function delete(Services $service): JsonResponse
    {
        $service->setDeletedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        $this->addFlash('success', 'Services has been deleted.');

        return new JsonResponse(['status' => 'ok', 'message' => 'Service has been deleted.']);
    }
}
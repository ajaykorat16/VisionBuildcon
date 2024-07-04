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
    ){
    }

    #[Route('/', name: '_list')]
    public function index(Request $request):Response
    {
        $searchForm = $this->createForm(SearchType::class);
        $searchForm->handleRequest($request);

        $searchData = $searchForm->get('search')->getData();

        $services = $this->servicesRepository->getActiveServices(
            $searchData,
            ServicesRepository::PAGE_SIZE,
            ServicesRepository::OFFSET,
        );

        return $this->render('admin/services/index.html.twig',[
            'services' => $services,
            'totalServices' => $this->servicesRepository->getTotalCountServices(),
            'search' => $searchForm->createView()
        ]);
    }

    #[Route('/load-more', name: '_load_more', options: ['expose' => true])]
    public function loadMore(Request $request): JsonResponse
    {
        $offset = (int) $request->query->get('offset');
        $services = $this->servicesRepository->getActiveServices(null, ServicesRepository::PAGE_SIZE, $offset);
        $content = [];

        foreach ($services as $service) {
            $content[] = $this->renderView('admin/project/list-items.html.twig', ['services' => $service]);
        }

        return new JsonResponse(['content' => $content], JsonResponse::HTTP_OK);
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

            $this->addFlash('success','New Service has been Created Successfully.');
            return $this->redirectToRoute('services_list');
        }

        return $this->render('admin/services/create.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: '_edit')]
    public function edit(Request $request, Services $service): Response
    {    
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $old_image = $request->request->get('old_photo');
            $new_image = $form->get('servicePhoto')->getData();

            if ($new_image) {
                if($old_image != $new_image){

                    $filePath = 'image/' . $old_image;

                    if (!empty($old_image) && file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                $service->setServicePhoto($new_image);

            }elseif($old_image){  

                $service->setServicePhoto($old_image);  
            }

            $this->entityManager->flush();
    
            $this->addFlash('success', sprintf('Service %d has been updated successfully.', $service->getId()));
            return $this->redirectToRoute('services_list');
        }
    
        return $this->render('admin/services/create.html.twig', [
            'form' => $form->createView(),
            'service' => $service,
        ]);
    }
    
    #[Route('/show/{id}', name: '_show')]
    public function show(Services $service): Response
    {
        return $this->render('admin/services/view.html.twig', [
            'services' => $service,
        ]);
    }

    #[Route('/delete/{id}', name: '_delete')]
    public function delete(Services $service): Response
    {
        $service->setDeletedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Services %s has been deleted successfully.', $service->getName()));

        return $this->redirectToRoute('services_list');
    }
}
<?php

namespace App\Controller\Admin;

use App\Entity\Request as EntityRequest;
use App\Repository\RequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin/request', name: 'request')]
class RequestController extends AbstractController
{
    public function __construct(
        private readonly RequestRepository $requestRepository,
        private readonly EntityManagerInterface $entityManager
    ){
    }

    #[Route('/', name: '_list')]
    public function index():Response{

        $request = $this->requestRepository->getRequests(RequestRepository::PAGE_SIZE, RequestRepository::OFFSET );

        return $this->render('admin/request/index.html.twig',[
            'request' => $request ,
            'totalRequests' => $this->requestRepository->getTotalCountRequest()
        ]);
    }

    #[Route('/load-more', name: '_load_more', options: ['expose' => true])]
    public function loadMore(Request $request): JsonResponse
    {
        $offset = (int) $request->query->get('offset');
        $requestEntity = $this->requestRepository->getRequests(RequestRepository::PAGE_SIZE, $offset);
        $content = [];

        foreach ($requestEntity as $requests) {
            $content[] = $this->renderView('admin/request/list-items.html.twig', ['requests' => $requests]);
        }

        return new JsonResponse(['content' => $content], JsonResponse::HTTP_OK);
    }

    #[Route('/delete/{id}' ,name: '_delete')]
    public function delete(EntityRequest $request): Response
    {
        $request->setDeletedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Request %s has been deleted successfully.', $request->getName()));
        
        return $this->redirectToRoute('request_list');
    } 
}
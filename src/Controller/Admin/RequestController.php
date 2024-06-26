<?php

namespace App\Controller\Admin;

use App\Repository\RequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin/request', name: 'request')]
class RequestController extends AbstractController
{
    public function __construct(
        private readonly RequestRepository $requestRepository
    )
    {
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

}
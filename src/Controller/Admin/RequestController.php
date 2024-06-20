<?php

namespace App\Controller\Admin;

use App\Repository\RequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        $request = $this->requestRepository->findAllRequests(0,RequestRepository::PAGE_SIZE);

        return $this->render('admin/request/index.html.twig',[
            'request' => $request ,
            'totalRequests' => $this->requestRepository->findAllRequestsCount()
        ]);
    }

    #[Route('/load-more', name: '_load_more', options: ['expose' => true])]
    public function loadMore(Request $request): Response
    {
        $offset = (int) $request->query->get('offset');
        $requestEntity = $this->requestRepository->findAllRequests($offset,RequestRepository::PAGE_SIZE);
        $content = '';

        foreach ($requestEntity as $requests) {
            $content .= $this->renderView('admin/request/list-items.html.twig', ['requests' => $requests]);
        }

        return new Response($content);
    }
}
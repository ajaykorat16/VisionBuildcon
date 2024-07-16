<?php

namespace App\Controller\FrontEnd;

use App\Repository\ClientRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/project', name: 'project')]
class ProjectController extends AbstractController
{
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly ClientRepository $clientRepository,
    ) {
    }

    #[Route('', name: '_index', requirements: ['' => '\d+'])]
    public function index(): Response
    {
        return $this->render('front-end/project/project.html.twig', [
            'totalProjects' => $this->projectRepository->getTotalCountsProjects(),
            'project' => $this->projectRepository->getActiveProjects(
                NULL,
                ProjectRepository::PAGE_SIZE,
                ProjectRepository::OFFSET
            ),
            'client' => $this->clientRepository->getClients(),
        ]);
    }

    #[Route('/load-more', name: '_content_load_more', options: ['expose' => true])]
    public function LoadMoreAction(Request $request): Response
    {
        $offset = (int) $request->query->get('offset');

        $project = $this->projectRepository->getActiveProjects(NULL, ProjectRepository::PAGE_SIZE, $offset);
        $content = [];

        foreach ($project as $projects) {
            $content[] = $this->renderView('front-end/project/project_content.html.twig', ['projects' => $projects]);
        }

        return new JsonResponse(['status' => 'OK', 'content' => $content]);
    }
}
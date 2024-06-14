<?php

namespace App\Controller\frontEnd;

use App\Entity\Request as RequestEntity;
use App\Form\RequestType;
use App\Repository\ClientRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    #[Route('', name: '_index')]
    public function index(): Response
    {
        return $this->render('front-end/project/project.html.twig', [
            'totalProjects' => $this->projectRepository->findAllActiveProjectsCount(),
            'project' => $this->projectRepository->findAllActiveProjects(
                0,
                ProjectRepository::PAGE_SIZE,
            ),
            'client' => $this->clientRepository->findActiveClients(),
        ]);
    }

    #[Route('/load-more', name: '_content_load_more', options: ['expose' => true])]
    public function contentLoadMore(Request $request): Response
    {
        $offset = (int) $request->query->get('offset');

        $project = $this->projectRepository->findAllActiveProjects($offset, ProjectRepository::PAGE_SIZE);
        $content = '';

        foreach ($project as $projects) {
            $content .= $this->renderView('front-end/project/project-content.html.twig', ['projects' => $projects]);
        }

        return new Response($content);
    }

    #[Route('/{name}', name: '_show')]
    public function show(string $name): Response
    {
        $originalName = str_replace('-', '#', $name);
        $project = $this->projectRepository->findOneBy(['name' => $originalName]);

        return $this->render('front-end/project/show.html.twig', [
            'project' => $project,
        ]);
    }

}
<?php

namespace App\Controller\Admin;

use App\Entity\Images;
use App\Form\ProjectType;
use App\Form\SearchType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Project;

#[Route('admin/projects', name: 'projects')]
class ProjectController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProjectRepository $projectRepository,
    ){
    }

    #[Route('/', name: '_list')]
    public function index(Request $request): Response
    {
        $searchForm = $this->createForm(SearchType::class);
        $searchForm->handleRequest($request);

        $searchData = $searchForm->get('search')->getData();

        $projects = $this->projectRepository->getActiveProjects(
            $searchData,
            ProjectRepository::PAGE_SIZE,
            ProjectRepository::OFFSET
        );
        
        return $this->render('admin/project/index.html.twig', [
            'project' => $projects,
            'totalProjects' => $this->projectRepository->getTotalCountsProjects(),
            'search' => $searchForm->createView()
        ]);
    }

    #[Route('/load-more', name: '_load_more', options: ['expose' => true])]
    public function loadMore(Request $request): JsonResponse
    {
        $offset = (int) $request->query->get('offset');
        $projects = $this->projectRepository->getActiveProjects(null, ProjectRepository::PAGE_SIZE, $offset);
    
        $content = [];
    
        foreach ($projects as $project) {
            $content[] = $this->renderView('admin/project/list-items.html.twig', ['project' => $project]);
        }
        
        return new JsonResponse(['content' => $content], JsonResponse::HTTP_OK);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Images = $form->get('hiddenimages')->getData();

            if($Images){
                $uploadedImages = explode(',', $Images);

                foreach ($uploadedImages as $tempFilename) {
                    $image = new Images();
                    $image->setImage($tempFilename);
                    $project->addImage($image);
                }
            }

            $this->entityManager->persist($project);
            $this->entityManager->flush();

            $this->addFlash('success', 'New project has been created successfully');

            return $this->redirectToRoute('projects_list');
        }

        return $this->render('admin/project/create.html.twig', [
            'form' => $form->createView(),
            'isProject' => false,
        ]);
    }

    #[Route('/edit/{id}', name: '_edit')]
    public function edit(Request $request, Project $project): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedImages = $form->get('hiddenimages')->getData();
            $submittedImageArray = $submittedImages ? explode(',', $submittedImages) : [];

            foreach ($project->getImages() as $image) {
                if (!in_array($image->getImage(), $submittedImageArray)) {
                    $this->entityManager->remove($image);
                }
            }

            foreach ($submittedImageArray as $tempFilename) {
                $imageExists = false;
                foreach ($project->getImages() as $image) {
                    if ($image->getImage() === $tempFilename) {
                        $imageExists = true;
                        break;
                    }
                }

                if (!$imageExists) {
                    $image = new Images();
                    $image->setImage($tempFilename);
                    $project->addImage($image);
                    $this->entityManager->persist($image);
                }
            }

            $this->entityManager->flush();

            $this->addFlash('success', sprintf('Project %d has been updated successfully.', $project->getId()));

            return $this->redirectToRoute('projects_list');
        }

        return $this->render('admin/project/create.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
            'isProject' => true,
        ]);
    }

    #[Route('/show/{id}', name: '_show')]
    public function show(Project $project):Response
    {
        return $this->render('admin/project/view.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/delete/{id}' ,name: '_delete')]
    public function delete(Project $project): Response
    {
        $project->setDeletedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Project %s has been deleted successfully.', $project->getName()));
        
        return $this->redirectToRoute('projects_list');
    } 
}
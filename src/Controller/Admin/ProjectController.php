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
    )
    {
    }

    #[Route('/', name: '_list')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $search = $form->get('search')->getData();

        $projects = $this->projectRepository->findAllActiveProjects(
            0,
            ProjectRepository::PAGE_SIZE,
            $search
        );
        return $this->render('admin/project/index.html.twig', [
            'project' => $projects,
            'totalProjects' => $this->projectRepository->findAllActiveProjectsCount(),
            'search' => $form->createView()
        ]);
    }

    #[Route('/load-more', name: '_load_more', options: ['expose' => true])]
    public function loadMore(Request $request): Response
    {
        $offset = (int) $request->query->get('offset');
        $project = $this->projectRepository->findAllActiveProjects($offset, ProjectRepository::PAGE_SIZE);
        $content = '';

        foreach ($project as $projects) {
            $content .= $this->renderView('admin/project/list-items.html.twig', ['project' => $projects]);
        }

        return new Response($content);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new = $request->request->all();

            if($new['project']['images']){
                if (!is_array($new['project']['images'])) {
                    $uploadedImages = [$new['project']['images']];
                } else {
                    $uploadedImages = explode(',', $new['project']['images']);
                }

                foreach ($uploadedImages as $tempFilename) {
                    $image = new Images();
                    $image->setImage($tempFilename);
                    $project->addImage($image);
                }
            }

            $this->entityManager->persist($project);
            $this->entityManager->flush();

            $this->addFlash('success', 'Project created successfully.');

            return $this->redirectToRoute('projects_list');
        }

        return $this->render('admin/project/create.html.twig', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }

    #[Route('/edit/{id}', name: '_edit')]
    public function edit(Request $request, Project $project): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new = $request->request->all();

            if (!$new['project']['images'] == null) {

                $uploadedImages = explode(',', $new['project']['images']);

                foreach ($uploadedImages as $tempFilename) {
                    $image = new Images();

                    if($tempFilename){
                        $existingImagePath = 'image/' . $image->getImage();

                        if (is_file($existingImagePath)) {
                            unlink($existingImagePath);
                        }

                        $image->setImage($tempFilename);
                    }
                    $project->addImage($image);
                }
            }
            $this->entityManager->flush();

            $this->addFlash('success', 'Project has been updated successfully.');
            return $this->redirectToRoute('projects_list');
        }

        return $this->render('admin/project/create.html.twig', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }

    #[Route('/show/{id}', name: '_show')]
    public function show(Project $project):Response
    {
        return $this->render('admin/project/view.html.twig', [
            'project' => $project,
        ]);
    }
    #[Route('/delete/{id}' ,name: '_delete' , options: ['expose' => true])]
    public function delete(Project $project): JsonResponse
    {
        $images = $project->getImages();

        foreach ($images as $image) {
            $imagePath = 'image/' . $image->getImage();

            if (is_file($imagePath)) {
                unlink($imagePath);
            }
        }

        $project->setDeletedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        $this->addFlash('success', 'Project has been deleted.');

        return new JsonResponse(['status' => 'ok', 'message' => 'Project has been deleted.']);
    }

}
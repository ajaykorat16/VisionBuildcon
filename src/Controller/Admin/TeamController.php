<?php

namespace App\Controller\Admin;

use App\Entity\Teams;
use App\Form\SearchType;
use App\Form\TeamType;
use App\Repository\TeamsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin/teams',name: 'teams')]
class TeamController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TeamsRepository $teamRepository
    )
    {
    }

    #[Route('/', name: '_list')]
    public function index(Request $request):Response
    {
        $searchForm = $this->createForm(SearchType::class);
        $searchForm->handleRequest($request);

        $searchData = $searchForm->get('search')->getData();

        $teams = $this->teamRepository->getActiveTeam(
            $searchData,
            TeamsRepository::PAGE_SIZE,
            TeamsRepository::OFFSET
        );

        return $this->render('admin/team/index.html.twig',[
            'teams' => $teams,
            'totalTeams' => $this->teamRepository->getTotalCountTeam(),
            'search' => $searchForm->createView()
        ]);
    }

    #[Route('/load-more', name: '_load_more' , options: ['expose' => true])]
    public function loadMore(Request $request): JsonResponse
    {
        $offset = (int) $request->query->get('offset');
        $teams = $this->teamRepository->getActiveTeam(null, TeamsRepository::PAGE_SIZE, $offset);
        $content = [];

        foreach ($teams as $team) {
            $content[] = $this->renderView('admin/team/list-items.html.twig', ['team' => $team]);
        }

        return new JsonResponse(['content' => $content], JsonResponse::HTTP_OK);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request):Response
    {
        $team = new Teams();

        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($team);
            $this->entityManager->flush();

            $this->addFlash('success','New team has been created successfully.');

            return $this->redirectToRoute('teams_list');
        }

        return $this->render('admin/team/create.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: '_edit')]
    public function edit(Request $request, Teams $team):Response
    {
    
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $old_image = $request->request->get('remove_photo');
            $new_image = $form->get('teamPhoto')->getData();

            if ($new_image) {
                if($old_image != $new_image)
                {
                    $filePath = 'image/' . $old_image;

                    if (!empty($old_image) && file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                $team->setTeamPhoto($new_image);

            }elseif($old_image){  

                $team->setTeamPhoto($old_image);  
            }

            $this->entityManager->flush();
            $this->addFlash('success','Team has been Updated Successfully.');

            return $this->redirectToRoute('teams_list');
        }

        return $this->render('admin/team/create.html.twig',[
            'form' => $form->createView(),
            'team' => $team
        ]);
    }

    #[Route('/show/{id}', name: '_show')]
    public function show(Teams $team): Response
    {
        return $this->render('admin/team/view.html.twig', [
            'team' => $team,
        ]);
    }

    #[Route('/delete/{id}', name: '_delete')]
    public function delete(Teams $team): Response
    {
        $team->setDeletedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        $this->addFlash('success', 'Team has been deleted successfully.');

        return $this->redirectToRoute('teams_list');
    }
}
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
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $search = $form->get('search')->getData();

        $teams = $this->teamRepository->findAllActiveTeams(
            0,
            TeamsRepository::PAGE_SIZE,
            $search
        );
        return $this->render('admin/team/index.html.twig',[
            'teams' => $teams,
            'totalTeams' => $this->teamRepository->findAllActiveTeamsCount(),
            'search' => $form->createView()
        ]);
    }

    #[Route('/load-more', name: '_load_more' , options: ['expose' => true])]
    public function loadMore(Request $request): Response
    {
        $offset = (int) $request->query->get('offset');
        $teams = $this->teamRepository->findAllActiveTeams($offset, TeamsRepository::PAGE_SIZE);
        $content = '';

        foreach ($teams as $team) {
            $content .= $this->renderView('admin/team/list-items.html.twig', ['team' => $team]);
        }

        return new Response($content);
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

            $this->addFlash('success','New team has Created Successfully...');

            return $this->redirectToRoute('teams_list');
        }

        return $this->render('admin/team/create.html.twig',[
            'form' => $form->createView(),
            'team' => $team
        ]);
    }

    #[Route('/edit/{id}', name: '_edit')]
    public function edit(Request $request, Teams $team):Response
    {
        if (file_exists($team->getTeamPhoto())) {
            unlink('image/' .$team->getTeamPhoto());
            $team->setTeamPhoto(null);
        }

        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();
            $this->addFlash('success','Team has Updated Successfully...');

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

    #[Route('/delete/{id}', name: '_delete', options: ['expose' => true])]
    public function delete(Teams $team): JsonResponse
    {
        $team->setDeletedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        $this->addFlash('success', 'Team has been deleted.');

        return new JsonResponse(['status' => 'ok', 'message' => 'Team has been deleted.']);
    }
}
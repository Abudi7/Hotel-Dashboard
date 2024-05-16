<?php

namespace App\Controller;

use App\Entity\Rooms;
use App\Form\Rooms1Type;
use App\Repository\RoomsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class RoomsController extends AbstractController
{
    #[Route('/rooms', name: 'app_rooms_index', methods: ['GET'])]
    public function index(RoomsRepository $roomsRepository): Response
    {
        return $this->render('rooms/index.html.twig', [
            'rooms' => $roomsRepository->findAll(),
        ]);
    }

    #[Route('rooms/new', name: 'app_rooms_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $room = new Rooms();
        $form = $this->createForm(Rooms1Type::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('app_rooms_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rooms/new.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }

    #[Route('rooms/{id}', name: 'app_rooms_show', methods: ['GET'])]
    public function show(Rooms $room): Response
    {
        return $this->render('rooms/show.html.twig', [
            'room' => $room,
        ]);
    }

    #[Route('rooms/{id}/edit', name: 'app_rooms_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rooms $room, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Rooms1Type::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rooms_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rooms/edit.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }

    #[Route('rooms/{id}', name: 'app_rooms_delete', methods: ['POST'])]
    public function delete(Request $request, Rooms $room, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($room);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rooms_index', [], Response::HTTP_SEE_OTHER);
    }
}

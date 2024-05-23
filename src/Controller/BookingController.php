<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Rooms;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class BookingController extends AbstractController
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route('/booking/new/{roomId}', name: 'app_booking_new')]
    public function new(Request $request, int $roomId): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the logged-in user
            $user = $this->getUser();

            // Check if the user is not null before accessing its properties
            if ($user !== null) {
                // Set the logged-in user's name as the customer name
                $customerName = $user->getUserIdentifier();
                $booking->setCustomername($customerName);
            } else {
                // If user is not authenticated, handle the situation accordingly (e.g., redirect to login)
                 return $this->redirectToRoute('app_login');
            }
            // Set the created_at timestamp to the current time in the local timezone
            $now = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
            $booking->setCreatedat($now);

            // Set the room
            $room = $this->entityManager->getRepository(Rooms::class)->find($roomId);
            $booking->setRooms($room);

            // Handle saving the booking to the database
            $this->entityManager->persist($booking);
            $this->entityManager->flush();

            // Redirect to a success page or any other desired page
            return $this->redirectToRoute('app_booking_success');
        }

        return $this->render('booking/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/booking/success', name: 'app_booking_success')]
    public function success(): Response
    {
        return $this->render('booking/success.html.twig');
    }


    #[Route('/booking', name: 'app_booking')]
    public function index(BookingRepository $bookingRepository): Response
    {
        $user = $this->getUser();
        $bookings = $bookingRepository->findBy(['customername' => $user->getUserIdentifier()]);

        return $this->render('booking/index.html.twig', [
            'bookings' => $bookings,
        ]);
    }
}


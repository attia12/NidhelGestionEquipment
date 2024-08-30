<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Service\CartService;
use App\Repository\EquipmentRepository;
use App\Repository\ReservationRepository;
use App\Service\WebSocketNotificationService;
use App\WebSocket\NotificationServer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CartController extends AbstractController
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add($id, EquipmentRepository $equipmentRepository): Response
    {
        $equipment = $equipmentRepository->find($id);
        if (!$equipment) {
            throw $this->createNotFoundException('The equipment does not exist');
        }

        $this->cartService->addToCart($id);

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart', name: 'cart_show')]
    public function show(EquipmentRepository $equipmentRepository): Response
    {
        $cart = $this->cartService->getCart();
        $cartItems = [];
        $totalQuantity = 0;

        foreach ($cart as $id => $quantity) {
            $cartItems[] = [
                'equipment' => $equipmentRepository->find($id),
                'quantity' => $quantity,
            ];
            $totalQuantity += $quantity;
        }

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
            'totalQuantity' => $totalQuantity,
        ]);
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove($id): Response
    {
        $this->cartService->removeFromCart($id);

        return $this->redirectToRoute('cart_show');
    }
    #[Route('/calandar', name: 'app_reservation_front', methods: ['GET'])]
    public function displayReservation(): Response
    {
        return $this->render('calandar/index.html.twig', [
            
        ]);
    }
    #[Route('/reservation/create', name: 'app_reservation_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, EquipmentRepository $equipmentRepository,WebSocketNotificationService $webSocketNotificationService): Response
    {
        $response = ['success' => false, 'message' => ''];
    
        // Retrieve equipment IDs from the request
        $equipmentIds = $request->request->get('equipmentIds');
        if (!$equipmentIds) {
            $response['message'] = 'No equipment selected for reservation.';
            return $this->json($response);
        }
    
        // Parse equipment IDs
        $equipmentIdsArray = explode(',', $equipmentIds);
        $equipmentItems = $equipmentRepository->findBy(['id' => $equipmentIdsArray]);
    
        if (empty($equipmentItems)) {
            $response['message'] = 'Invalid equipment selected.';
            return $this->json($response);
        }
    
        // Create a reservation for each equipment
        $startTime = new \DateTime($request->request->get('startTime'));
        $endTime = new \DateTime($request->request->get('endTime'));
       
    
        foreach ($equipmentItems as $equipment) {
            $reservation = new Reservation();
            $reservation->setEquipment($equipment);
            $reservation->setStartTime($startTime);
            $reservation->setEndTime($endTime);
           
    
            $entityManager->persist($reservation);
        }
    
        $entityManager->flush();
        $this->cartService->clearCart();
       
       
       
      
       
    
        $response['success'] = true;
        $response['message'] = 'Reservations created successfully.';
        $webSocketNotificationService->notifyClients($response['message']);
        
    
        return $this->json($response);
    }

    #[Route('/test', name: 'app_test_front', methods: ['GET'])]
    public function notification(): Response
    {
        return $this->render('calandar/test.html.twig', [
            
        ]);
    }

    #[Route('/reservation_front', name: 'app_reservation_list_front', methods: ['GET'])]
public function list(Request $request,ReservationRepository $reservationRepository): JsonResponse
{
    $startDate = $request->query->get('start');
    $endDate = $request->query->get('end');

    $reservations = $reservationRepository->findReservationsInDateRange($startDate, $endDate);

    $events = [];
    foreach ($reservations as $reservation) {
        $events[] = [
            'title' => $reservation->getEquipment()->getName(), // Assuming Equipment has a name property
            'start' => $reservation->getStartTime()->format('Y-m-d\TH:i:s'),
            'end' => $reservation->getEndTime()->format('Y-m-d\TH:i:s'),
        ];
    }

    return new JsonResponse($events);
}

    

 
}

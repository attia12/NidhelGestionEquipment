<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Entity\Feedback;
use App\Form\RatingType;
use App\Repository\CategoryRepository;
use App\Repository\EquipmentRepository;
use App\Repository\FeedbackRepository;
use App\Repository\ReservationRepository;
use App\Service\ProfanityFilterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EquipementController extends AbstractController
{
    #[Route('/equipement', name: 'app_equipement')]
    public function index(EquipmentRepository $equipmentRepository,FeedbackRepository $feedbackRepository): Response
    {
        $availableEquipment = $equipmentRepository->findBy(['availabilityStatus' => 'available']);
        $approvedFeedback = $feedbackRepository->findAllApproved();
      

        return $this->render('equipement/front.html.twig', [
            'equipment' => $availableEquipment,
            'feedback' => $approvedFeedback,
        ]);
    }
    #[Route('/back/equipement', name: 'app_equipement_back')]
    public function back(): Response
    {
        return $this->render('backend.html.twig', [
            
        ]);
    }
    #[Route('/equipment/{id}/rate', name: 'equipment_rate', methods: ['POST'])]
    public function rate(Request $request, Equipment $equipment, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $rating = $data['rating'] ?? null;
    
        if ($rating !== null) {
            $equipment->setRating((int)$rating);
            $entityManager->flush();
    
            return $this->json(['success' => true]);
        }
    
        return $this->json(['success' => false], Response::HTTP_BAD_REQUEST);
    }
    #[Route('/submit-feedback', name: 'submit_feedback', methods: ['POST'])]
    public function submitFeedback(Request $request, EntityManagerInterface $entityManager,ProfanityFilterService $profanityFilterService ): Response
    {
        // Get form data
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $message = $request->request->get('message');
        $filteredText = $profanityFilterService->filterText($message);

        // Create a new Feedback entity
        $feedback = new Feedback();
        $feedback->setName($name);
        $feedback->setEmail($email);
        $feedback->setMessage($filteredText);
        
        // Persist and flush to save the feedback
        $entityManager->persist($feedback);
        $entityManager->flush();

        // Optionally, add a flash message or redirect
        $this->addFlash('success', 'Feedback submitted successfully!');

        return $this->redirectToRoute('app_equipement');
    }
    #[Route('/', name: 'app_dahsboard')]
    public function viewdahsboard(FeedbackRepository $feedbackRepository, ReservationRepository $reservationRepository,EquipmentRepository $equipmentRepository,
    CategoryRepository $categoryRepository): Response
    {
        $feedbackCount = $feedbackRepository->count([]);
        $reservationCount = $reservationRepository->count([]);
        $equipmentCount = $equipmentRepository->count([]);
        $categoryCount = $categoryRepository->count([]);
        $monthlyFeedbackData = $feedbackRepository->getMonthlyFeedbackCounts();
        $labels = array_keys($monthlyFeedbackData);
        $values = array_values($monthlyFeedbackData);
        $categories = $categoryRepository->findAll();
        $categoryData = [];
        foreach ($categories as $category) {
            $equipmentCount = $category->getEquipment()->count();
            $categoryData[] = [
                'category' => $category->getName(),
                'count' => $equipmentCount
            ];
        }

        return $this->render('equipement/dashboard.html.twig', [
            'feedbackCount' => $feedbackCount,
            'reservationCount' => $reservationCount,
            'equipmentCount' => $equipmentCount,
            'categoryCount' => $categoryCount,
            'monthlyFeedbackData' => [
                'labels' => $labels,
                'values' => $values,
            ],
            'categoryData' => $categoryData
        ]);
      

        
    }
    

}

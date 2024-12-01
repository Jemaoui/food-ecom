<?php

namespace App\Controller\Client;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Service\Order\OrderCreateService;
use App\Service\Order\OrderUpdateService;
use App\Service\Order\OrderDeleteService;
use App\Service\Order\OrderDetailsService;
use App\Service\Order\OrderPdfService;
use App\Service\Order\OrderStatusService;
use App\Service\Payement\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/order')]
final class OrderController extends AbstractController
{
    public function __construct(
        private OrderCreateService $orderCreateService,
        private OrderUpdateService $orderUpdateService,
        private OrderDeleteService $orderDeleteService,
        private OrderPdfService $orderPdfService,
        private AuthorizationCheckerInterface $authChecker,
    ) {}

    #[Route(name: 'order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to view your orders.');
        }

        // Si l'utilisateur n'a pas le rôle ROLE_CLIENT, lui refuser l'accès
        if (!in_array('ROLE_CLIENT', $user->getRoles())) {
            throw new AccessDeniedException('You do not have permission to access this page.');
        }

        // Récupérer les commandes uniquement pour l'utilisateur connecté si c'est un client
        $orders = $orderRepository->findBy(['customer' => $user]);

        return $this->render('Client/order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[IsGranted('view', 'order')]
    #[Route('/{id}', name: 'order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
 
        return $this->render('Client/order/show.html.twig', [
            'order' => $order,
        ]);
    }

   
    #[IsGranted('edit', 'order')]    
    #[Route('/{id}/edit', name: 'order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order): Response
    {
     
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->orderUpdateService->update($order);

            return $this->redirectToRoute('order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[IsGranted('delete', 'order')]
    #[Route('/{id}', name: 'order_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order): Response
    {

        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
            $this->orderDeleteService->delete($order);
        }

        return $this->redirectToRoute('order_index', [], Response::HTTP_SEE_OTHER);
    }

    #[IsGranted('pdf', 'order')]
    #[Route('/{id}/pdf', name: 'order_pdf', methods: ['GET'])]
    public function pdf(Order $order): Response
    {

        return $this->orderPdfService->generate($order);
    }






    #[Route('/order/confirmation/{orderId}', name: 'order_confirmation')]
    public function confirmation(
        int $orderId,
        StripeService $stripeService,
        OrderDetailsService $orderService,
    ): Response {
        // Récupérer la commande
        $order = $orderService->getOrderById($orderId);

        if (!$order) {
            throw $this->createNotFoundException('Order not found.');
        }

        // Créer une session Stripe Checkout
        $session = $stripeService->createCheckoutSession($order->getId(), $order->getTotalAmount());

        return $this->render('Client/order/confirmation.html.twig', [
            'stripe_public_key' => $stripeService->getPublicKey(),
            'session_id' => $session->id,
            'order' => $order,
        ]);
    }



    #[Route('/order/success/{orderId}', name: 'order_success')]
    public function success(int $orderId, OrderDetailsService $orderService, OrderStatusService $orderStatusService): Response
    {
        // Récupérer la commande avec l'ID passé en paramètre
        $order = $orderService->getOrderById($orderId);

        if (!$order) {
            throw $this->createNotFoundException('Order not found.');
        }

        // Mettre à jour le statut de la commande en "paid"
        $orderStatusService->updateStatus($order, 'paid');

        // Rendre la vue de succès
        return $this->render('Client/order/success.html.twig', [
            'order' => $order, // Passer l'ordre à la vue pour l'affichage si nécessaire
        ]);
    }

    #[Route('/order/cancel', name: 'order_cancel')]
    public function cancel(): Response
    {
        return $this->render('Client/order/cancel.html.twig');
    }
}

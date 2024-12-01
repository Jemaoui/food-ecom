<?php

namespace App\Controller\Back;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Service\Order\OrderCreateService;
use App\Service\Order\OrderUpdateService;
use App\Service\Order\OrderDeleteService;
use App\Service\Order\OrderStatusService;
use App\Service\Order\OrderPdfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/admin/order')]
final class OrderController extends AbstractController
{
    public function __construct(
        private OrderCreateService $orderCreateService,
        private OrderUpdateService $orderUpdateService,
        private OrderDeleteService $orderDeleteService,
        private OrderPdfService $orderPdfService
    ) {}

    #[Route(name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('Back/order/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->orderCreateService->create($order);

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Back/order/new.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('Back/order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->orderUpdateService->update($order);

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Back/order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order): Response
    {
        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
            $this->orderDeleteService->delete($order);
        }

        return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/pdf', name: 'app_order_pdf', methods: ['GET'])]
    public function pdf(Order $order): Response
    {
        return $this->orderPdfService->generate($order);
    }


    #[Route('/{id}/change-status', name: 'app_order_change_status', methods: ['POST'])]
    public function changeStatus(Request $request, Order $order, OrderStatusService $orderStatusService): Response
    {
        $newStatus = $request->request->get('status');
        $token = $request->request->get('_token');

        // Vérification du token CSRF
        if (!$this->isCsrfTokenValid('change_status' . $order->getId(), $token)) {
            throw new AccessDeniedException('Invalid CSRF token');
        }

        // Mettre à jour le statut via le service
        $orderStatusService->updateStatus($order, $newStatus);

        // Ajouter un message flash pour l'utilisateur
        $this->addFlash('success', 'Order status updated successfully!');

        // Rediriger vers la page de la liste des commandes
        return $this->redirectToRoute('app_order_index');
    }
}

<?php

namespace App\Service\Order;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class OrderStatusService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Met à jour le statut d'une commande.
     *
     * @param Order $order
     * @param string $newStatus
     */
    public function updateStatus(Order $order, string $newStatus): void
    {
        $order->setStatus($newStatus);
        $this->entityManager->flush();
    }
}

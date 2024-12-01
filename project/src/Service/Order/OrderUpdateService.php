<?php

namespace App\Service\Order;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class OrderUpdateService
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function update(Order $order): void
    {
        // Logique de mise à jour
        $this->entityManager->flush();
    }
}

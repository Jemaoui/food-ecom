<?php
namespace App\Service\Order;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class OrderDeleteService
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function delete(Order $order): void
    {
        // Logique de suppression
        $this->entityManager->remove($order);
        $this->entityManager->flush();
    }
}

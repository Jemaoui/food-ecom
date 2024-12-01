<?php
namespace App\Service\Order;


use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class OrderDetailsService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getOrderById(int $orderId): ?Order
    {
        return $this->entityManager->getRepository(Order::class)->find($orderId);
    }
}
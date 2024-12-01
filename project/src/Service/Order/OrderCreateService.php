<?php
namespace App\Service\Order;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class OrderCreateService
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function create(Order $order): void
    {
        // Logique de création, validation ou autre traitement spécifique si nécessaire
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}

<?php
namespace App\Service\Category;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class CategoryDeletionService
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function delete(Category $category): void
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }
}

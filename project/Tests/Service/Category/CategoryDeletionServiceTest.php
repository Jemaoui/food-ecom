<?php

namespace App\Tests\Service\Category;

use App\Entity\Category;
use App\Service\Category\CategoryDeletionService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CategoryDeletionServiceTest extends TestCase
{
    private $entityManager;
    private $categoryDeletionService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->categoryDeletionService = new CategoryDeletionService($this->entityManager);
    }

    public function testDeleteCategory()
    {
        $category = new Category();
        $category->setName('Test Category');
        $category->setDescription('This is a test category.');

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($category));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->categoryDeletionService->delete($category);

        // Vérifier que la catégorie n'existe plus
        $this->assertNull($category->getId(), 'La catégorie ne doit plus avoir d\'ID après la suppression.');
    }
}

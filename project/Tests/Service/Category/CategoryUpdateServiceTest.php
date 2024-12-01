<?php

namespace App\Tests\Service\Category;

use App\Entity\Category;
use App\Service\Category\CategoryUpdateService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\FileUploader;
use PHPUnit\Framework\TestCase;

class CategoryUpdateServiceTest extends TestCase
{
    private $entityManager;
    private $fileUploader;
    private $categoryUpdateService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->fileUploader = $this->createMock(FileUploader::class);
        $this->categoryUpdateService = new CategoryUpdateService($this->entityManager, $this->fileUploader);
    }

    public function testUpdateCategoryWithoutImage()
    {
        $category = new Category();
        $category->setName('Test Category');
        $category->setDescription('This is a test category.');

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->categoryUpdateService->update($category, null);

        // Vérifier que la catégorie existe et que ses champs sont correctement définis

        $this->assertEquals('Test Category', $category->getName(), 'Le nom de la catégorie doit être "Test Category".');
        $this->assertEquals('This is a test category.', $category->getDescription(), 'La description de la catégorie doit être "This is a test category".');
        $this->assertNull($category->getImage(), 'L\'image de la catégorie doit être null.');
    }

    public function testUpdateCategoryWithImage()
    {
        $category = new Category();
        $category->setName('Test Category');
        $category->setDescription('This is a test category.');

        $imageFile = $this->createMock(UploadedFile::class);
        $newFilename = 'new_filename.jpg';

        $this->fileUploader->expects($this->once())
            ->method('upload')
            ->with($this->equalTo($imageFile))
            ->willReturn($newFilename);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->categoryUpdateService->update($category, $imageFile);

        // Vérifier que la catégorie existe et que ses champs sont correctement définis
      
        $this->assertEquals('Test Category', $category->getName(), 'Le nom de la catégorie doit être "Test Category".');
        $this->assertEquals('This is a test category.', $category->getDescription(), 'La description de la catégorie doit être "This is a test category".');
        $this->assertEquals($newFilename, $category->getImage(), 'L\'image de la catégorie doit être "new_filename.jpg".');
    }
}

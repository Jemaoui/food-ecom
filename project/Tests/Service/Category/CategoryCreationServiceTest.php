<?php

namespace App\Tests\Service\Category;

use App\Entity\Category;
use App\Service\Category\CategoryCreationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\FileUploader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

class CategoryCreationServiceTest extends TestCase
{
    private $entityManager;
    private $fileUploader;
    private $categoryCreationService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->fileUploader = $this->createMock(FileUploader::class);
        $this->categoryCreationService = new CategoryCreationService($this->entityManager, $this->fileUploader);
    }

    public function testCreateCategoryWithoutImage()
    {
        $category = new Category();
        $category->setName('Test Category');
        $category->setDescription('This is a test category.');

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($category));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->categoryCreationService->create($category, null);

        $this->assertNull($category->getImage());
        $this->assertEquals('Test Category', $category->getName(), 'Le nom de la catégorie doit être "Test Category".');
        $this->assertEquals('This is a test category.', $category->getDescription(), 'La description de la catégorie doit être "This is a test category".');
        $this->assertNull($category->getImage(), 'L\'image de la catégorie doit être null.');
    }

    public function testCreateCategoryWithImage()
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
            ->method('persist')
            ->with($this->equalTo($category));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->categoryCreationService->create($category, $imageFile);

        $this->assertEquals($newFilename, $category->getImage());
    }
}

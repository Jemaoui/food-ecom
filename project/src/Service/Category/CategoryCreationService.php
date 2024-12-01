<?php

namespace App\Service\Category;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\FileUploader;

class CategoryCreationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileUploader $fileUploader
    ) {}

    public function create(Category $category, ?UploadedFile $imageFile): void
    {
        if ($imageFile) {
            $newFilename = $this->fileUploader->upload($imageFile);
            $category->setImage($newFilename);
        }

        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }
}

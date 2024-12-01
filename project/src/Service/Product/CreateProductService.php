<?php

namespace App\Service\Product;

use App\Entity\Product;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;

class CreateProductService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileUploader $imageUploader
    ) {}

    public function create(Product $product, $imageFile = null): void
    {
        if ($imageFile) {
            $newFilename = $this->imageUploader->upload($imageFile);
            $product->setImage($newFilename);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}

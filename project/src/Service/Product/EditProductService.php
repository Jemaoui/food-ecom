<?php

namespace App\Service\Product;

use App\Entity\Product;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;

class EditProductService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileUploader $imageUploader
    ) {}

    public function edit(Product $product, $imageFile = null): void
    {
        if ($imageFile) {
            $newFilename = $this->imageUploader->upload($imageFile);
            $product->setImage($newFilename);
        }

        $this->entityManager->flush();
    }
}

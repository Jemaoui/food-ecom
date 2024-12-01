<?php

namespace App\Service\Product;

use App\Repository\ProductRepository;
use App\Entity\Product;

class ShowProductService
{
    public function __construct(private ProductRepository $productRepository) {}

    public function getProduct(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }
}


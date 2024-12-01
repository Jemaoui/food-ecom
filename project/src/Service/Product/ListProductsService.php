<?php

namespace App\Service\Product;

use App\Repository\ProductRepository;

class ListProductsService
{
    public function __construct(private ProductRepository $productRepository) {}

    public function getAllProducts(): array
    {
        return $this->productRepository->findAll();
    }
}


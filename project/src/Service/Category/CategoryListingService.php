<?php

namespace App\Service\Category;

use App\Repository\CategoryRepository;

class CategoryListingService
{
    public function __construct(private CategoryRepository $categoryRepository) {}

    public function getAllCategories(): array
    {
        return $this->categoryRepository->findAll();
    }
}

<?php

namespace App\Controller\Front;

use App\Entity\Category;
use App\Form\CategoryType;

use App\Service\Category\CategoryCreationService;
use App\Service\Category\CategoryUpdateService;
use App\Service\Category\CategoryDeletionService;
use App\Service\Category\CategoryListingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/category')]
final class CategoryController extends AbstractController
{
    #[Route(name: 'category_index', methods: ['GET'])]
    public function index(CategoryListingService $listingService): Response
    {
        $categories = $listingService->getAllCategories();

        return $this->render('Front/Category/index.html.twig', [
            'categories' => $categories,
        ]);
    }
 
 
  

    #[Route('/{id}', name: 'category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('Front/Category/show.html.twig', [
            'category' => $category,
        ]);
    }
}

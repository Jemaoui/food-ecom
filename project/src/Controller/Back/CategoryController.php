<?php

namespace App\Controller\Back;

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


#[Route('/admin/category')]
final class CategoryController extends AbstractController
{
    #[Route(name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryListingService $listingService): Response
    {
        $categories = $listingService->getAllCategories();

        return $this->render('Back/category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryCreationService $creationService): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            $creationService->create($category, $imageFile);

            return $this->redirectToRoute('app_category_index');
        }

        return $this->render('Back/category/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Category $category,
        CategoryUpdateService $updateService
    ): Response {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            $updateService->update($category, $imageFile);

            return $this->redirectToRoute('app_category_index');
        }

        return $this->render('Back/category/edit.html.twig', [
            'form' => $form,
            'category' => $category
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Category $category,
        CategoryDeletionService $deletionService
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $deletionService->delete($category);
        }

        return $this->redirectToRoute('app_category_index');
    }

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('Back/category/show.html.twig', [
            'category' => $category,
        ]);
    }
}

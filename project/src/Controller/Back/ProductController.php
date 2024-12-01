<?php

namespace App\Controller\Back;

use App\Entity\Product;
use App\Form\ProductType;
use App\Service\Product\ListProductsService;
use App\Service\Product\CreateProductService;
use App\Service\Product\ShowProductService;
use App\Service\Product\EditProductService;
use App\Service\Product\DeleteProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/product')]
final class ProductController extends AbstractController
{
    public function __construct(
        private ListProductsService $listProductsService,
        private CreateProductService $createProductService,
        private ShowProductService $showProductService,
        private EditProductService $editProductService,
        private DeleteProductService $deleteProductService
    ) {}

    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(): Response
    {
        $products = $this->listProductsService->getAllProducts();

        return $this->render('Back/product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            $this->createProductService->create($product, $imageFile);

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('Back/product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $product = $this->showProductService->getProduct($id);

        return $this->render('Back/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            $this->editProductService->edit($product, $imageFile);

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('Back/product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $this->deleteProductService->delete($product);
        }

        return $this->redirectToRoute('app_product_index');
    }
}

<?php

namespace App\Controller\Front;

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

#[Route('/product')]
final class ProductController extends AbstractController
{
    public function __construct(
        private ListProductsService $listProductsService,
        private CreateProductService $createProductService,
        private ShowProductService $showProductService,
        private EditProductService $editProductService,
        private DeleteProductService $deleteProductService
    ) {}

    #[Route(name: 'product_index', methods: ['GET'])]
    public function index(): Response
    {
        $products = $this->listProductsService->getAllProducts();

        return $this->render('Front/Product/index.html.twig', [
            'products' => $products,
        ]);
    }

 
    #[Route('/{id}', name: 'product_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $product = $this->showProductService->getProduct($id);

        return $this->render('Front/Product/show.html.twig', [
            'product' => $product,
        ]);
    }
 

 
}

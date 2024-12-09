<?php

namespace App\Controller\Front;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class HomeController extends AbstractController
{

    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }


    #[Route('/', name: 'app_home')]
    public function index(
        CategoryRepository $categoryRepository, 
        ProductRepository $productRepository, 
        CacheInterface $cache
    ): Response {
        // Mise en cache des catégories
        $categories = $cache->get('categories_cache', function (ItemInterface $item) use ($categoryRepository) {
            $item->expiresAfter(3600); // Cache pendant 1 heure
            return $categoryRepository->findAll();
        });

        // Mise en cache des produits bestsellers
        $products = $cache->get('bestseller_products_cache', function (ItemInterface $item) use ($productRepository) {
            $item->expiresAfter(1800); // Cache pendant 30 minutes
            return $productRepository->findBy(['isBestseller' => true]);
        });

        return $this->render('Front/index.html.twig', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }


    #[Route('/faq', name: 'app_faq')]
    public function faq(): Response
    {
        return $this->render('Front/faq.html.twig', []);
    }



    public function onKernelRequest(RequestEvent $event)
    {
        $cartTotalQuantity = $this->cartService->getTotalQuantity();
        $event->getRequest()->attributes->set('cartTotalQuantity', $cartTotalQuantity);
    }
}

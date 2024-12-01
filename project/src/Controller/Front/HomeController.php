<?php

namespace App\Controller\Front;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }


    #[Route('/', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository, ProductRepository $productRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $products =  $productRepository->findBy(['isBestseller' => true]);

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

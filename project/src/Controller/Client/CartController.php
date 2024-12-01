<?php

namespace App\Controller\Client;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\ProductRepository;
use App\Service\Cart\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{



    private $productRepository;
    private EntityManagerInterface $entityManager;
    private CartService $cartService;
    private Security $security;

    public function __construct(
        CartService $cartService,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->cartService = $cartService;
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }


    #[Route('/cart', name: 'app_cart')]
    public function showCart(): Response
    {
        $cart = $this->cartService->getCart(); // Format : [productId => ['quantity' => value]]
        $products = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = $this->productRepository->find($productId);
            if ($product) {
                $quantity = $item['quantity'];
                $price = $product->getPrice();

                $products[] = [
                    'name' => $product->getName(),
                    'price' => $price,
                    'quantity' => $quantity,
                    'total' => $price * $quantity,
                    'image' => $product->getImage(),
                    'id' => $product->getId(),
                ];
                $total += $price * $quantity;
            }
        }

        return $this->render('cart/index.html.twig', [
            'products' => $products,
            'total' => $total,
        ]);
    }





    #[Route('/add-to-cart/{id}', name: 'add_to_cart')]
    public function addToCart(int $id): RedirectResponse
    {
        // Vérifiez si le produit existe
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        // Ajouter le produit au panier
        $this->cartService->addToCart($id);

        // Rediriger vers la page du panier ou une autre page
        return $this->redirectToRoute('app_cart');
    }

    #[Route("/cart/checkout", name: "cart_checkout")]
    public function checkout()
    {

             // Vérifiez si l'utilisateur est authentifié
             $user = $this->security->getUser();

             if (!$user) {
                 // Si l'utilisateur n'est pas authentifié, redirigez vers la page d'enregistrement
                 return new RedirectResponse($this->generateUrl('app_register')); // Remplacez 'register' par le nom de la route d'inscription
             }
     

             

        $cart = $this->cartService->getCart();
        if (empty($cart)) {
            return $this->redirectToRoute('app_cart');
        }

        // Convert cart to order
        $order = $this->createOrderFromCart($cart, $user);

        // Save order in the database
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        // Clear the cart session
        $this->cartService->clearCart();

        // Redirect to order confirmation page
        return $this->redirectToRoute('order_confirmation', ['orderId' => $order->getId()]);
    }

    private function createOrderFromCart(array $cart, $user)
    {


   

        // Create an Order object and populate it with cart data
        $order = new Order();
        $totalAmount = 0;

        foreach ($cart as $productId => $item) {
            // Fetch the product from the database using its ID
            $product = $this->productRepository->find($productId);

            if (!$product) {
                // If product is not found, skip this item 
                continue;
            }

            $orderItem = new OrderItem();
            $orderItem->setProduct($product);
            $orderItem->setPrice($product->getPrice());
            $orderItem->setQuantity($item['quantity']);

            $totalAmount += $product->getPrice() * $item['quantity'];

            $order->addOrderItem($orderItem);
        }

        $order->setTotalAmount($totalAmount);

        $order->setCustomer($user);
        return $order;
    }

    #[Route('/clear-cart ', name: 'clear_cart')]
    public function clearCart(): RedirectResponse
    {
        // Vérifiez si le produit existe

        // Ajouter le produit au panier
        $this->cartService->clearCart();

        // Rediriger vers la page du panier ou une autre page
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/update-cart/{id}/{quantity}', name: 'update_cart')]
    public function updateCart(int $id, int $quantity): JsonResponse
    {
        $this->cartService->updateQuantity($id, $quantity);
        return new JsonResponse(['status' => 'success']);
    }

    #[Route('/remove-from-cart/{id}', name: 'remove_from_cart')]
    public function removeFromCart(int $id): JsonResponse
    {
        $this->cartService->removeFromCart($id);
        return new JsonResponse(['status' => 'success']);
    }

 
}

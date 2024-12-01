<?php

namespace App\Service\Cart;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private $session;
    private const CART_KEY = 'cart';

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }


    public function addToCart(int $productId, int $quantity = 1): void
    {
        // Récupérer le panier de la session
        $cart = $this->session->get('cart', []);
    
        // Si le produit est déjà dans le panier, augmenter la quantité
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            // Initialiser avec la quantité ajoutée
            $cart[$productId] = ['quantity' => $quantity];
        }
    
        // Enregistrer le panier dans la session
        $this->session->set('cart', $cart);
    }
    

    public function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    public function removeFromCart(int $productId): void
    {
        $cart = $this->session->get('cart', []);
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $this->session->set('cart', $cart);
        }
    }

    public function clearCart(): void
    {
        $this->session->remove('cart');
    }


    public function updateQuantity(int $productId, int $quantity): void
    {
        // Récupérer le panier actuel
        $cart = $this->getCart();

        // Vérifier si le produit existe dans le panier
        if (isset($cart[$productId])) {
            // Mettre à jour la quantité
            $cart[$productId]['quantity'] = $quantity;
        }

        // Sauvegarder le panier mis à jour dans la session
        $this->session->set('cart', $cart);
    }

    public function getTotalQuantity(): int
    {
        $cart = $this->session->get(self::CART_KEY, []);

        $totalQuantity = 0;
        foreach ($cart as $item) {
            $totalQuantity += $item['quantity'];
        }

        return $totalQuantity;
    }
 

    
}

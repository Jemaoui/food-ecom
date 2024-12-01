<?php
namespace App\Service\Payement;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeService
{
    private string $stripeSecretKey;
    private string $stripePublicKey;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct( UrlGeneratorInterface $urlGenerator)
    {
        $this->stripeSecretKey = "sk_test_51QRAyQJHWfBX42VPHWmzHKw8P61shzmpLcC0CdfSRVUXST6QHfeGN6ZCOnSAk8heWzVeBnxIn0FFA94tIyk3rAvO00ve9P6DtE";
        $this->stripePublicKey = "pk_test_51QRAyQJHWfBX42VPsWHQa83pw8CEUVASHeGvzSgislwOzssbW7TQNWvd8cxIoET7DpKMSJ4z5ZMhYPq18jnwRfsK00qmbgO2qU";
        $this->urlGenerator = $urlGenerator;


        Stripe::setApiKey($this->stripeSecretKey);
    }

    public function createCheckoutSession(int $orderId, float $amount): Session
    {
  

        return $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Order #' . $orderId,
                        ],
                        'unit_amount' => $amount * 100, 
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'client_reference_id' => $orderId, 
            'success_url' => $this->urlGenerator->generate('order_success', ['orderId' => $orderId], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->urlGenerator->generate('order_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

    }

    public function getPublicKey(): string
    {
        return $this->stripePublicKey;
    }
}

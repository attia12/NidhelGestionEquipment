<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function addToCart($equipmentId)
    {
        $cart = $this->session->get('cart', []);
        if (isset($cart[$equipmentId])) {
            $cart[$equipmentId]++;
        } else {
            $cart[$equipmentId] = 1;
        }
        $this->session->set('cart', $cart);
    }

    public function getCart()
    {
        return $this->session->get('cart', []);
    }

    public function removeFromCart($equipmentId)
    {
        $cart = $this->session->get('cart', []);
        if (isset($cart[$equipmentId])) {
            unset($cart[$equipmentId]);
        }
        $this->session->set('cart', $cart);
    }

    public function clearCart()
    {
        $this->session->set('cart', []);
    }
}
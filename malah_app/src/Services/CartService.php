<?php

namespace App\Services;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private $session;
    private $repoProduct;
    private $tva = 0.2;

    public function __construct(SessionInterface $session, ProductRepository $repoProduct)
    {
        $this->session = $session;
        $this->repoProduct = $repoProduct;
    }

    public function addToCart($id)
    {
        $cart = $this->getCart();

        if (isset($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $this->updateCart($cart);
    }

    public function deleteFromCart($id)
    {
        $cart = $this->getCart();
        if (isset($cart[$id])) {
            // Produit déjà dans le panier
            if ($cart[$id] > 1) {
                // Produit existe plus d'une fois
                $cart[$id]--;
            } else {
                unset($cart[$id]);
            }
            $this->updateCart($cart);
        }
    }

    public function deleteAllFromCart($id)
    {
        $cart = $this->getCart();
        if (isset($cart[$id])) {
            unset($cart[$id]);
            $this->updateCart($cart);
        }
    }

    public function deleteCart()
    {
        $this->updateCart([]);
    }

    public function updateCart($cart)
    {
        $this->session->set('cart', $cart);
        $this->session->set('cartData', $this->getFullCart());
    }

    public function getCart()
    {
        return $this->session->get('cart', []);
    }

    public function getFullCart()
    {
        $cart = $this->getCart();

        $fullCart = [];
        $cartQuantity = 0;
        $subTotal = 0;

        foreach ($cart as $id => $quantity) {
            $product = $this->repoProduct->find($id);

            if ($product) {
                $fullCart["products"][] = [
                    "quantity" => $quantity,
                    "product" => $product
                ];
                $cartQuantity += $quantity;
                $subTotal += $quantity * $product->getPrice() / 100;
            } else {
                $this->deleteFromCart($id);
            }
        }

        $fullCart["data"] = [
            "cart_quantity" => $cartQuantity,
            "subTotalHT" => $subTotal,
            "taxe" => round($subTotal * $this->tva, 2),
            "subTotalTTC" => round($subTotal + ($subTotal * $this->tva), 2),

        ];

        return $fullCart;
    }
}

<?php

namespace App\Controller;

use App\Services\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

// Le panier est sauvegardé dans une session 

class CartController extends AbstractController
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function index(): Response
    {
        $cart = $this->cartService->getFullCart();

        if (!isset($cart['products'])) {
            return $this->redirectToRoute('home');
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     */
    public function addToCart($id): Response
    {
        $this->cartService->addToCart($id);
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete")
     */
    public function deleteFromCart($id): Response
    {
        $this->cartService->deleteFromCart($id);
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/delete-all/{id}", name="cart_delete_all_from_id")
     */
    public function deleteAllFormCart($id): Response
    {
        $this->cartService->deleteAllFromCart($id);
        return $this->redirectToRoute('cart');
    }
}

<?php

namespace App\Controller;

use App\Form\CheckoutType;
use App\Services\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/checkout")
 */

class CheckoutController extends AbstractController
{

    private $cartService;
    private $session;

    public function __construct(CartService $cartService, SessionInterface $session)
    {
        $this->cartService = $cartService;
        $this->session = $session;
    }

    /**
     * @Route("/", name="checkout")
     */
    public function index(Request $request): Response
    {
        $cart = $this->cartService->getFullCart();
        $user = $this->getUser();

        if (!isset($cart['products'])) {
            return $this->redirectToRoute('home');
        }

        if (!$user->getAddresses()->getValues()) {
            $this->addFlash(
                'checkout_message',
                'Please add an adress to your account before continuing !'
            );
            return $this->redirectToRoute('address_new');
        }

        if ($this->session->get('checkout_data')) {
            return $this->redirectToRoute('checkout_confirm');
        }

        $form = $this->createForm(CheckoutType::class, null, ['user' => $user]);

        return $this->render('checkout/index.html.twig', [
            'controller_name' => 'CheckoutController',
            'cart' => $cart,
            'checkout' => $form->createView()
        ]);
    }

    /**
     * @Route("/confirm", name="checkout_confirm")
     */
    public function confirm(Request $request): Response
    {
        $cart = $this->cartService->getFullCart();
        $user = $this->getUser();


        if (!isset($cart['products'])) {
            return $this->redirectToRoute('home');
        }

        if (!$user->getAddresses()->getValues()) {
            $this->addFlash(
                'checkout_message',
                'Please add an adress to your account before continuing !'
            );
            return $this->redirectToRoute('address_new');
        }

        $form = $this->createForm(CheckoutType::class, null, ['user' => $user]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() || $this->session->get('checkout_data')) {

            if ($this->session->get('checkout_data')) {
                $data =  $this->session->get('checkout_data');
            } else {
                $data = $form->getData();
                $this->session->set('checkout_data', $data);
            }

            $adress = $data['address'];
            $carrier = $data['carrier'];
            $informations = $data['informations'];

            return $this->render('checkout/confirm.html.twig', [
                'controller_name' => 'CheckoutController',
                'cart' => $cart,
                'checkout' => $form->createView(),
                'address' => $adress,
                'carrier' => $carrier,
                'informations' => $informations
            ]);
        }

        return $this->redirectToRoute('checkout');
    }

    /**
     * @Route("/edit", name="checkout_edit")
     */
    public function FunctionName(): Response
    {
        $this->session->set('checkout_data', []);
        return $this->redirectToRoute('checkout');
    }
}

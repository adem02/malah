<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Services\CartService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\AddressRepository as RepositoryAddressRepository;

/**
 * @Route("/address")
 */
class AddressController extends AbstractController
{

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/", name="address_index", methods={"GET"})
     */
    public function index(RepositoryAddressRepository $adressRepository): Response
    {
        return $this->render('address/index.html.twig', [
            'addresses' => $adressRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="address_new", methods={"GET","POST"})
     */
    public function new(Request $request, CartService $cartService): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $address->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($address);
            $entityManager->flush();

            if ($cartService->getFullCart()) {
                return $this->redirectToRoute('checkout');
            }

            $this->addFlash('address_message', 'Your address has been saved');
            return $this->redirectToRoute('account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('address/new.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="address_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Address $address): Response
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            if ($this->session->get('checkout_data')) {
                $data = $this->session->get('checkout_data');
                $data['address'] = $address;
                $this->session->set('checkout_data', $data);
                return $this->redirectToRoute('checkout_confirm');
            }
            $this->addFlash('address_message', 'Your address has been edited');
            return $this->redirectToRoute('account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('address/edit.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="address_delete", methods={"POST"})
     */
    public function delete(Request $request, Address $address): Response
    {
        if ($this->isCsrfTokenValid('delete' . $address->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($address);
            $entityManager->flush();

            $this->addFlash('address_message', 'Your address has been deleted');
        }

        return $this->redirectToRoute('account', [], Response::HTTP_SEE_OTHER);
    }
}

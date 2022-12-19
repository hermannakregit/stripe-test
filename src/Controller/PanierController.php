<?php

namespace App\Controller;

use App\Entity\Checkout;
use App\Repository\CheckoutRepository;
use App\service\StripePaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController

{
    #[Route('/', name: 'app_home')]
    public function index(SessionInterface $session, CheckoutRepository $checkoutRepository): Response
    {

        $panier = $session->get("panier", []);

        if(empty($panier)){

            for ($i=1; $i < 5 ; $i++) { 
                array_push($panier, [
                    "id" => $i,
                    "nom" => "Fromage {$i}",
                    "prix" => $i * 10,
                    "description" => "Lorem ipsu Some quick example text to build on thek of the card's content.",
                ]);
            }

            $id = uniqid();

            $newPanier = [
                'id' => $id,
                'products' => $panier,
            ];

            $session->set("panier", $newPanier);

        }

        $panier = $session->get("panier", []);

        $checkout = $checkoutRepository->findBy(['checkout_id' => $panier['id']], ['id' => 'DESC'], 1);

        $payment_id = null;
        if(!empty($checkout) && $checkout[0]->isCompleted()){
            $payment_id = $checkout[0]->getStripeId();
        }

        shuffle($panier['products']);

        $context = [
            'panier' => $panier['products'],
            'payment_id' => $payment_id
        ];
        
        return $this->render('index.html.twig', $context);
    }


    #[Route('/payment/checkout', name: 'app_payment')]
    public function payment(SessionInterface $session, EntityManagerInterface $em): Response
    {

        $panier = $session->get("panier", []);

        if(!empty($panier)){

            $payment_session = new StripePaymentService($this->getParameter('stripe_payment_key'));
            $payment = $payment_session->startPayment($panier);

            $checkout = new Checkout();
            $checkout->setStripeId($payment->id);
            $checkout->setCheckoutId($panier['id']);
            $em->persist($checkout);
            $em->flush();

            return $this->redirect($payment->url);
           
        }else{
            return $this->redirectToRoute("app_home");
        }
        
    }

}

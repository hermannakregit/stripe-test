<?php

namespace App\Controller;

use App\service\StripePaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController

{
    #[Route('/', name: 'app_home')]
    public function index(SessionInterface $session): Response
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

            $session->set("panier", $panier);
        }

        shuffle($panier);
        
        return $this->render('index.html.twig', compact('panier'));
    }


    #[Route('/payment/checkout', name: 'app_payment')]
    public function payment(SessionInterface $session): Response
    {

        $panier = $session->get("panier", []);

        if(!empty($panier)){

            $payment_session = new StripePaymentService($this->getParameter('stripe_payment_key'));
            $payment = $payment_session->startPayment($panier);

            return $this->redirect($payment->url, Response::HTTP_SEE_OTHER);
           
        }else{
            return $this->redirectToRoute("app_home");
        }
        
    }

    #[Route('/payment/link', name: 'app_payment_link')]
    public function paymentLink(SessionInterface $session): Response
    {

        $panier = $session->get("panier", []);

        if(!empty($panier)){

            $payment_link = new StripePaymentService($this->getParameter('stripe_payment_key'));
            $payment = $payment_link->linkPayment($panier);

            return $this->redirectToRoute("app_home");
           
        }else{
            return $this->redirectToRoute("app_home");
        }
        
    }
}

<?php

namespace App\Controller;

use App\Repository\CheckoutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{

    #[Route('/payment/success', name: 'app_stripe_success')]
    public function success(): Response
    {
        $this->addFlash('success', "Votre Paiment est effectué avec succès, merci de votre confiance.");
        return $this->redirectToRoute("app_home");
    }

    #[Route('/payment/cancel', name: 'app_stripe_cancel')]
    public function cancel(): Response
    {
        $this->addFlash('warning', "Votre Paiment à echoué.");
        return $this->redirectToRoute("app_home");
    }


    #[Route('/webhook/stripe', name: 'app_stripe_webhook', methods: 'POST')]
    public function webhookStripe(Request $request, CheckoutRepository $checkoutRepository, EntityManagerInterface $em): Response
    {
        $signature = $request->headers->get('stripe-signature');
        $body = (string)$request->getContent();

        $event = Webhook::constructEvent(
            $body,
            $signature,
            $this->getParameter('stripe_webhook_key')
        );

        if($event->type == "checkout.session.completed"){
            
            $payment_id = $event->data['object']['id'];
            
            $checkout = $checkoutRepository->findOneBy(['stripe_id' => $payment_id]);
            $checkout->setCompleted(true);
            $em->persist($checkout);
            $em->flush();

        }
        
        return new Response();
    }
}

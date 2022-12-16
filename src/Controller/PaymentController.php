<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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


    #[Route('/stripe/webhook', name: 'app_stripe_webhook')]
    public function index(): Response
    {
        return $this->render('stripe_webhook/index.html.twig', [
            'controller_name' => 'StripeWebhookController',
        ]);
    }
}

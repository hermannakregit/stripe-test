<?php

namespace App\service;

use Stripe\Stripe;
use Stripe\PaymentLink;
use Stripe\Checkout\Session;

class StripePaymentService
{

	public function __construct(string $key)
	{
		Stripe::setApiKey($key);
		Stripe::setApiVersion('2022-11-15');
	}

	public function startPayment(array $panier)
	{
		
		$session = Session::create([
			'line_items' => [
				array_map(fn (array $product) => [
					'quantity' => 1,
					'price_data' => [
						'currency' => 'EUR',
						'product_data' => [
							'name' => $product['nom'],
						],
						'unit_amount' => $product['prix']
					],
				], $panier['products'])
			],

			'mode' => 'payment',
			'success_url' => 'https://127.0.0.1:8000/payment/success',
			'cancel_url' => 'https://127.0.0.1:8000/payment/cancel',
			'billing_address_collection' => 'required',
			'shipping_address_collection' => [
				'allowed_countries' => ['FR', 'CI', 'BE'],
			],
			'metadata' => [
				'panier' => $panier['id'],
			]
		]);

		return $session;

	}

}
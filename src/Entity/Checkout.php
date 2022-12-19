<?php

namespace App\Entity;

use App\Repository\CheckoutRepository;
use App\Trait\Timestamp;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CheckoutRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table('checkouts')]
class Checkout
{
    use Timestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $stripe_id = null;

    #[ORM\Column(length: 255)]
    private ?string $checkout_id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;


    #[ORM\Column(nullable: true)]
    private ?bool $completed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStripeId(): ?string
    {
        return $this->stripe_id;
    }

    public function setStripeId(string $stripe_id): self
    {
        $this->stripe_id = $stripe_id;

        return $this;
    }

    public function getCheckoutId(): ?string
    {
        return $this->checkout_id;
    }

    public function setCheckoutId(string $checkout_id): self
    {
        $this->checkout_id = $checkout_id;

        return $this;
    }

    public function isCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

}

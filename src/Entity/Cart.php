<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CartRepository")
 */
class Cart
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="purchasedItem", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PurchaseItem", mappedBy="cart")
     */
    private $purchasedItems;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PurchaseItem", mappedBy="cart")
     */
    private $purchaseItems;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->purchasedItem = new ArrayCollection();
        $this->purchaseItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getPurchasedItems(): Collection
    {
        return $this->purchasedItems;
    }

    public function addPurchasedItem(PurchaseItem $purchasedItem): self
    {
        $this->purchasedItems[] = $purchasedItem;

        return $this;
    }

    public function removePurchasedItem(PurchaseItem $purchasedItem): self
    {
        if ($this->purchasedItem->contains($purchasedItem)) {
            $this->purchasedItem->removeElement($purchasedItem);
            // set the owning side to null (unless already changed)
            if ($purchasedItem->getCart() === $this) {
                $purchasedItem->setCart(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PurchaseItem[]
     */
    public function getPurchaseItems(): Collection
    {
        return $this->purchaseItems;
    }

    public function addPurchaseItem(PurchaseItem $purchaseItem): self
    {
        if (!$this->purchaseItems->contains($purchaseItem)) {
            $this->purchaseItems[] = $purchaseItem;
            $purchaseItem->setCart($this);
        }

        return $this;
    }

    public function removePurchaseItem(PurchaseItem $purchaseItem): self
    {
        if ($this->purchaseItems->contains($purchaseItem)) {
            $this->purchaseItems->removeElement($purchaseItem);
            // set the owning side to null (unless already changed)
            if ($purchaseItem->getCart() === $this) {
                $purchaseItem->setCart(null);
            }
        }

        return $this;
    }
}

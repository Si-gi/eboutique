<?php


namespace App\Repository;


//src/Repository/UserRepository.php
use App\Entity\PurchaseItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
/**
 * @method PurchaseItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseItem[]    findAll()
 * @method PurchaseItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseItemRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PurchaseItem::class);
    }
}
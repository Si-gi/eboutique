<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\User;
use App\Entity\Cart;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Description;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CartController extends AbstractController
{

    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $categoryRepository;
    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $productRepository;
    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $cartRepository;
    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $purchasedItemRepository;
    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $userRepository;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->categoryRepository = $entityManager->getRepository(Category::class);
        $this->productRepository = $entityManager->getRepository(Product::class);
        $this->cartRepository = $entityManager->getRepository(Cart::class);
        $this->userRepository = $entityManager->getRepository(User::class);
        $this->purchasedItemRepository = $entityManager->getRepository(PurchaseItem::class);
    }

    /**
     * @Route("/ezzerz/{id}", name="homepage")
     */
    public function addItem($id, Request $request){
        if(!$this->getUser()){
            return $this->redirectToRoute("fos_user_security_login", array($request->attributes->get('_route')));
        }
        $user = $this->getUser();
        $cart =   $user->getCart();
        if($cart == null){
            $cart = new Cart($user);
        }
        $product = $this->productRepository->find($id);

        $cart = $this->cartRepository->find($user->getCart());
        $purchased_items = $cart->getPurchasedItems();

        foreach ($purchased_items->toArray() as $purchased_item){
            $pi = $this->purchasedItemRepository->find($purchased_item);
            if( $pi->getProduct() == $product){
                $purchased = true;
                $pi->setQuantity($pi->getQuantity()+1);
                break;
            }
        }
        if(!$purchased){
            $pi->setQuantity(1);
            $pi = new PurchaseItem();
            $purchased = false;
        }

        $pi->setProduct($product);
        $pi->setCart($cart);
        $cart->addPurchasedItem($pi);
        $this->entityManager->persist($pi);
        $this->entityManager->flush($pi);
        $this->entityManager->persist($cart);
        $this->entityManager->flush($cart);
        return $this->redirectToRoute("product",array("id" =>$id));
    }

    /**
     * @Route("/cart/{id}", name="cart")
     */
    public function checkout($id){
        $user =$this->userRepository->find($this->getUser());
        $cart = $this->cartRepository->find($user->getCart());

        $purchased_items = $cart->getPurchasedItems();
        $purchased_items = $purchased_items->toArray();
        $total_quantity = 0;
        $t_price = 0;
        foreach ($purchased_items as $item){
            $total_quantity += $item->getQuantity();
            $t_price += $item->getProduct()->getPrice() * $item->getQuantity();
        }
        return $this->render("checkout.html.twig",
            [
                't_price' => $t_price,
                'qte' => $total_quantity,
                'items' => $purchased_items,
                'categorys' => $this->categoryRepository->findAll()

            ]);
    }

    /**
     * @Route("/update_qte", name="update_quantity")
     */
    public function updateQuantity(Request $request){

        if($request->request->get('id') && $request->request->get('qte')){
            $purchased_item = $this->purchasedItemRepository->find( $request->request->get('id'));
            $purchased_item->setQuantity($purchased_item->getQuantity()+ $request->request->get('qte'));
            $this->entityManager->persist($purchased_item);
            $this->entityManager->flush();

            $arrData = ['output' => $request->request->get('id'), "qte" => $purchased_item->getQuantity()];
            return new JsonResponse($arrData);
        }
        $arrData = ['error' => $request];
        return new JsonResponse($arrData);
    }

    /**
     * @Route("/delete_item", name="delete_item")
     */
    public function deleteItem(Request $request){

        if($request->request->get('id')){
            $purchased_item = $this->purchasedItemRepository->find( $request->request->get('id'));
            $this->entityManager->remove($purchased_item);
            $this->entityManager->flush();

            $arrData = ['output' => $request->request->get('id')];
            return new JsonResponse($arrData);
        }
        $arrData = ['error' => $request];
        return new JsonResponse($arrData);
    }
}
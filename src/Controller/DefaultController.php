<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Entity\Product;

/**
 * @package App\Controller
 * @Route("/article", name="article")
 */

class DefaultController  extends AbstractController
{

    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $categoryRepository;
    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $productRepository;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->categoryRepository = $entityManager->getRepository(Category::class);
        $this->productRepository = $entityManager->getRepository(Product::class);
    }

    /**
     * @Route("/homepage", name="homepage")
     */
    public function index()
    {
        $products = $this->productRepository->findAll();
        $categorys = $this->categoryRepository->findAll();
        return $this->render('index.html.twig', [
            'categorys' => $categorys,
            'products' => $products
        ]);
    }

    /**
     * @Route("/product/{id}", name="product")
     */
    public function product($id,Request $request){
        $product = $this->productRepository->find($id);
        $comments = $product->getComments();

        if($this->getUser() != null)
        {
            $Comment = new Comment();
            $user = $this->getUser();
            $Comment->setUser($user);
            $Comment->setProduct($product);
            $Comment->setApproved(true);
            $form = $this->createForm(CommentType::class, $Comment);
            $form->handleRequest($request);

            // Check is valid
            if ($form->isSubmitted() && $form->isValid()) {

                $this->entityManager->persist($Comment);

                $this->entityManager->flush($Comment);

                $this->addFlash('success', 'Commentaire posté');

            }
            return $this->render("product.html.twig",
                [
                    'form' => $form->createView(),
                    'comments' => $comments,
                    'product' => $product,
                    'categorys' => $categorys = $this->categoryRepository->findAll()

                ]);
        }
        return $this->render("product.html.twig",
            [
                'comments' => $comments,
                'product' => $product,
                'categorys' => $categorys = $this->categoryRepository->findAll()

            ]);



    }
    /**
     * @Route("/category/{category}", name="category")
     */
    public function category($category){

        $category = $this->categoryRepository->find($category);//new Category($category);

        $products = $category->getProducts();
        //        $products = $this->productRepository->getProductByCategorys($category);
        // $products = Product::getProductByCategorys($category);
        $categorys = $this->categoryRepository->findAll();

        return $this->render("category.html.twig",
        [
            'products' => $products,
            'categorys' => $categorys
        ]);
    }

}
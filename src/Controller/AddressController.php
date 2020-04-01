<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\BlogPost;
use App\Entity\User;
use App\Form\addressType;
use App\Form\ArticleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $userRepository;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    /**
     * @Route("/add_address", name="add_address")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAddress(Request $request){
        $address = new Address();

        $user = $this->userRepository->find($this->getUser());

        $address->setUser($user);


        $form = $this->createForm(addressType::class, $address);
        $form->handleRequest($request);

        // Check is valid
        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($address);
            $this->entityManager->flush();
            $this->addFlash('success', 'Adresse enregistré');

            return $this->redirectToRoute('address');
        }

        return $this->render('addressform.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function getAddress(Request $request){

        $arrData = ['output' => $request->request->get('id')];
        return new JsonResponse($arrData);
    }

    public function address(){
        return $this->render('addresses.html.twig');
    }
}
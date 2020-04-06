<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\User;
use App\Form\addressType;
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
    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $addressRepository;


    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $entityManager->getRepository(User::class);
        $this->addressRepository = $entityManager->getRepository(Address::class);

    }

    /**
     * @Route("/add_address", name="add_address")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAddress(Request $request){
        if($this->getUser() == null){
          return $this->redirectToRoute('fos_user_security_login');
        }
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

        return $this->render('addressForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function getAddress(Request $request){
        if($this->getUser() == null){
            return $this->redirectToRoute('fos_user_security_login');
        }
        $adress = $this->addressRepository->find($request->request->get('id'));

        $arrData = [
                    'name' => $adress->getName(),
                    'address' => $adress->getAddress(),
                    'phone' => $adress->getPhone(),
                    'cp'    => $adress->getCp(),
                    'city' => $adress->getCity(),
                    'country' => $adress->getCountry()
                    ];
        return new JsonResponse($arrData);
    }

    public function address(){
        if($this->getUser() == null){
            return $this->redirectToRoute('fos_user_security_login');
        }
        return $this->render('addresses.html.twig',
        ['adre' => $this->getUser()->getAddress()]);
    }
}
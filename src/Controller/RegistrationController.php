<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\RegistrationFormType;
use App\Entity\User;

class RegistrationController extends AbstractController
{
    /**
     * Show register form and perform User registration
     *
     * @Route("/register", name="registration_index")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $apiToken = hash('SHA256', $user->getUsername());
            $user->setApiToken($apiToken);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully registered!');
            return $this->redirectToRoute('default_index');
        }

        return $this->render('registration.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
<?php

namespace App\Controller\Api;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Entity\User;

class UserController extends ApiController
{
    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    /**
    * @Route("/user/{id}")
    */
    public function getUserAction(Request $request, UserRepository $userRepository): JsonResponse
    {
        if(is_numeric($request->get('id'))){
            $userId = $request->get('id');

            $user = $this->userRepository->findOneById($userId);

            if(!$user)
                return $this->respondNotFound('User not found!');

            return $this->respond([
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail()
                ]
            );
        } elseif (!is_numeric($request->get('id'))) {
            
            return $this->respondValidationError('Invalid data type!');
        } else {

            return $this->respondValidationError('Bad request!');
        }
    }

    /**
    * @Route("/users", methods="POST")
    */
    public function createUserAction(Request $request): JsonResponse
    {
        $requestData = $this->getRequestData($request);
        if(!empty($requestData['error']))
            return $this->respondFound($requestData['error']);

        $user = new User;
        $user->setUsername($requestData['username']);
        $user->setEmail($requestData['email']);
        $password = $this->passwordEncoder->encodePassword($user, $requestData['password']);
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->respondCreated();
    }
}
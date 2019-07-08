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
    * @Route("/user/{id}", methods="GET")
    */
    public function getUserAction(Request $request): JsonResponse
    {
        $userExists = $this->validateByIdIfUserExists((int) $request->get('id'));
        if(!$userExists)
            return $this->respondNotFound('User not found!');
            
        $user = $this->userRepository->findOneById($request->get('id'));

        return $this->respond([
                'username' => $user->getUsername(),
                'email' => $user->getEmail()
            ]
        );
    }

    /**
    * @Route("/users", methods="POST")
    */
    public function createUserAction(Request $request): JsonResponse
    {
        $requestData = $this->getRequestData($request);
        if(!empty($requestData['error']))
            return $this->respondFound($requestData['error']);

        $userExists = $this->validateByEmailIfUserExists($requestData);
        if($userExists)
            return $this->respondFound('User already exists!');

        $user = new User;
        $user->setUsername($requestData['username']);
        $user->setEmail($requestData['email']);
        $password = $this->passwordEncoder->encodePassword($user, $requestData['password']);
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->respondCreated();
    }

    /**
     * @Route("/users/{id}", methods="PUT")
     */
    public function updateUserAction(string $id, Request $request): JsonResponse
    {
        $createAction = false;
        $requestData = $this->getRequestData($request, $createAction);
        $userExists = $this->validateByIdIfUserExists((int) $id);
        if(!$userExists)
            return $this->respondNotFound('User not found!');
            
        $user = $this->userRepository->findOneById($id);
        if(isset($requestData['username']))
            $user->setUsername($requestData['username']);

        if(isset($requestData['email']))
            $user->setEmail($requestData['email']);

        if(isset($requestData['password'])){
            $password = $this->passwordEncoder->encodePassword($user, $requestData['password']);
            $user->setPassword($password);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->respondUpdated();
    }

    /**
     * @Route("/users/{id}", methods="DELETE")
     */
    public function deleteUserAction(string $id, Request $request): JsonResponse
    {
        $userExists = $this->validateByIdIfUserExists((int) $id);
        if(!$userExists)
            return $this->respondNotFound('User not found!');

        $user = $this->userRepository->findOneById($id);

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->respondDeleted();
    }
}
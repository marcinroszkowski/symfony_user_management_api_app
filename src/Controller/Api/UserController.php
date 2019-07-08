<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Entity\User;

class UserController extends ApiController
{
    /**
    * @Route("/user/{id}")
    */
    public function getUserAction(Request $request, UserRepository $userRepository): JsonResponse
    {
        if(is_numeric($request->get('id'))){
            $userId = $request->get('id');

            $user = $userRepository->findOneById($userId);

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
}
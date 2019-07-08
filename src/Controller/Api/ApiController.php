<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Entity\User;

class ApiController
{
    /**
     * @var integer HTTP status code - 200 (OK) by default
     */
    protected $statusCode = 200;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Gets the value of statusCode.
     *
     * @return integer
     */
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    /**
     * Sets the value of statusCode.
     *
     * @param integer $statusCode the status code
     *
     * @return self
     */
    protected function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Returns a JSON response
     *
     * @param array $data
     * @param array $headers
     *
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function respond(array $data, array $headers = []): JsonResponse
    {
        return new JsonResponse($data, $this->getStatusCode(), $headers);
    }

    /**
     * Sets an error message and returns a JSON response
     *
     * @param string $errors
     *
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function respondWithErrors(string $errors, array $headers = []): JsonResponse
    {
        $data = array(
            'errors' => $errors,
        );

        return new JsonResponse($data, $this->getStatusCode(), $headers);
    }

    /**
     * Returns a HTTP Error 401 Unauthorized request
     *
     * @param string $message
     *
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function respondUnauthorized(string $message = 'Authorization failed!'): JsonResponse
    {
        return $this->setStatusCode(401)->respondWithErrors($message);
    }

    /**
     * Returns a HTTP Error 400
     *
     * @param string $message
     *
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function respondValidationError(string $message = 'Validation errors'): JsonResponse
    {
        return $this->setStatusCode(400)->respondWithErrors($message);
    }

    /**
     * Returns a HTTP Error 404 Not Found
     *
     * @param string $message
     *
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function respondNotFound(string $message = 'Not found!'): JsonResponse
    {
        return $this->setStatusCode(404)->respondWithErrors($message);
    }

    /**
     * Returns a HTTP Status 302 Found
     *
     * @param string $message
     *
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function respondFound(string $message): JsonResponse
    {
        return $this->setStatusCode(302)->respondWithErrors($message);
    }

    /**
     * Returns a HTTP status 201 Created
     *
     * @param array $data
     *
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function respondCreated(string $message = 'User created!'): JsonResponse
    {
        return $this->setStatusCode(201)->respond(array('message' => $message));
    }

    /**
     * Returns a HTTP status 200 OK
     *
     * @param array $data
     *
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function respondUpdated(string $message = 'User updated!'): JsonResponse
    {
        return $this->respond(array('message' => $message));
    }

    /**
     * Validates provided request data
     *
     * @param array $data
     *
     * @return array 
     */
    protected function validateRequestData(array $data, $createAction): array
    {
        $validatedRequestData = array();

        if($createAction == true){
            if(! isset($data['username'])) {
                $validatedRequestData['error'] = 'Provide username!';            
                return $validatedRequestData;
            } elseif (! isset($data['email'])) {
                $validatedRequestData['error'] = 'Provide email!';
                return $validatedRequestData;
            } elseif(! isset($data['password'])){
                $validatedRequestData['error'] = 'Provide password!';            
                return $validatedRequestData;
            }
        }

        if(isset($data['username']) && is_string($data['username']) && strlen($data['username'] <= User::USERNAME_LENGTH ))
            $validatedRequestData['username'] = $data['username'];
        
        if(isset($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL) && strlen($data['email'] <= User::EMAIL_LENGTH ))
            $validatedRequestData['email'] = $data['email'];

        if(isset($data['password']) && is_string($data['password']) && strlen($data['password'] <= User::PASSWORD_LENGTH))
            $validatedRequestData['password'] = $data['password'];

        return $validatedRequestData;
    }

    /**
     * Returns json_decoded request data in array
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    protected function getRequestData(Request $request, $createAction = true): array
    {
        $requestData = json_decode($request->getContent(), 1);
        $validatedRequestData = $this->validateRequestData($requestData, $createAction);

        return $validatedRequestData;
    }

    /**
     * Validates by id whether User exists and returns corresponding bool value
     *
     * @param int $id
     *
     * @return bool
     */
    protected function validateByIdIfUserExists(int $id): bool
    {
        $user = $this->userRepository->findOneById($id);
        return $userExists = ($user) ? true : false;
    }

    /**
     * Validates by email whether User exists and returns corresponding bool value
     *
     * @param array $requestDate
     *
     * @return bool
     */
    protected function validateByEmailIfUserExists(array $requestData): bool
    {
        $user = $this->userRepository->findOneByEmail($requestData['email']);
        return $userExists = ($user) ? true : false;
    }
}
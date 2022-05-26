<?php

namespace BeyondCapable\Application\Controller\Api;

use BeyondCapable\Entity\Admin\User;
use Psr\Log\LoggerInterface;
use BeyondCapable\Repository\Admin\UserRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

/**
 * Class AuthController
 *
 * @package App\Controller\Api
 */
class AuthController extends ApiController
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * AuthController constructor.
     *
     * @param UserRepository $userRepository
     * @param LoggerInterface $logger
     */
    public function __construct(UserRepository $userRepository, LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;

        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        $request = $this->transformJsonBody($request);

        $firstName = $request->get('firstname');
        $lastName = $request->get('lastname');
        $username = $request->get('username');
        $password = $request->get('password');
        $email = $request->get('email');

        if (empty($username) || empty($password) || empty($email)) {
            return $this->respondValidationError("Invalid Username or Password or Email");
        }

        try {
            $user = new User($username);

            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setPassword($encoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setUsername($username);
            $this->userRepository->add($user, true);
        } catch (OptimisticLockException | ORMException $e) {
            $this->logger->error($e->getMessage());
        }

        return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getUsername()));
    }

    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }
}

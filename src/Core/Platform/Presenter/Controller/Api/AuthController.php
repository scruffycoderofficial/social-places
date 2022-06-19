<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Presenter\Controller\Api
{
    use BeyondCapable\Component\Security\Domain\Entity\User;

    use Psr\Log\LoggerInterface;

    use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

    use Doctrine\ORM\Exception\ORMException;
    use Doctrine\ORM\OptimisticLockException;

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
         * AuthController constructor.
         *
         * @param LoggerInterface $logger
         */
        public function __construct(LoggerInterface $logger)
        {
            $this->logger = $logger;
        }

        #[Route('/api/register', name: 'register')]
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

                $user = User::create($username);

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

        #[Route('/api/login_check', name: 'api_login_check')]
        public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager): JsonResponse
        {
            return new JsonResponse(['token' => $JWTManager->create($user)]);
        }
    }
}

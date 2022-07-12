<?php

namespace Oro\Bundle\SyncBundle\Security;

use Oro\Bundle\SyncBundle\Authentication\Ticket\TicketDigestGenerator\TicketDigestGeneratorInterface;
use Oro\Bundle\SyncBundle\Security\Token\AnonymousTicketToken;
use Oro\Bundle\SyncBundle\Security\Token\TicketToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken as Token;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * The authentication provider for Sync authentication ticket.
 */
class TicketAuthenticationProvider implements AuthenticationProviderInterface
{
    /** @var TicketDigestGeneratorInterface */
    private $ticketDigestGenerator;

    /** @var UserProviderInterface */
    private $userProvider;

    /** @var string */
    private $providerKey;

    /** @var string */
    private $secret;

    /** @var int */
    private $ticketTtl;

    /**
     * @param TicketDigestGeneratorInterface $ticketDigestGenerator
     * @param UserProviderInterface          $userProvider
     * @param string                         $providerKey
     * @param string                         $secret
     * @param int                            $ticketTtl
     */
    public function __construct(
        TicketDigestGeneratorInterface $ticketDigestGenerator,
        UserProviderInterface $userProvider,
        string $providerKey,
        string $secret,
        int $ticketTtl
    ) {
        $this->userProvider = $userProvider;
        $this->ticketDigestGenerator = $ticketDigestGenerator;
        $this->providerKey = $providerKey;
        $this->secret = $secret;
        $this->ticketTtl = $ticketTtl;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(TokenInterface $token)
    {
        $this->validateTokenCreatedDate($token);

        $password = $this->secret;
        $user = $this->fetchUser($token);
        if (null !== $user) {
            $password = $user->getPassword();
        }

        $nonce = $token->getAttribute('nonce');
        $created = $token->getAttribute('created');
        $ticketDigest = $token->getCredentials();
        $expectedDigest = $this->ticketDigestGenerator->generateDigest($nonce, $created, $password);
        if ($ticketDigest !== $expectedDigest) {
            throw new BadCredentialsException(sprintf(
                'Ticket "%s" for "%s" is not valid - invalid digest.',
                $token->getCredentials(),
                $token->getUsername()
            ));
        }

        return null !== $user
            ? new TicketToken($user, $ticketDigest, $this->providerKey, $user->getRoles())
            : new AnonymousTicketToken($ticketDigest, self::USERNAME_NONE_PROVIDED);
    }

    /**
     * {@inheritDoc}
     */
    public function supports(TokenInterface $token)
    {
        return
            $token instanceof Token
            && $token->hasAttribute('nonce')
            && $token->hasAttribute('created')
            && $this->providerKey === $token->getProviderKey();
    }

    /**
     * @param TokenInterface $token
     */
    private function validateTokenCreatedDate(TokenInterface $token): void
    {
        $created = $token->getAttribute('created');

        $createdTime = strtotime($created);
        $now = strtotime(date('c'));
        if ($createdTime > $now) {
            throw new BadCredentialsException(sprintf(
                'Ticket "%s" for "%s" is not valid, because token creation date "%s" is in future',
                $token->getCredentials(),
                $token->getUsername(),
                $created
            ));
        }

        if ($now - $createdTime > $this->ticketTtl) {
            throw new BadCredentialsException(sprintf(
                'Ticket "%s" for "%s" is expired',
                $token->getCredentials(),
                $token->getUsername()
            ));
        }
    }

    /**
     * @param TokenInterface $token
     *
     * @return UserInterface|null
     */
    private function fetchUser(TokenInterface $token): ?UserInterface
    {
        $user = null;
        $username = $token->getUsername();
        if ($username) {
            try {
                $user = $this->userProvider->loadUserByUsername($username);
            } catch (UsernameNotFoundException $e) {
                throw new BadCredentialsException(sprintf(
                    'Ticket "%s" for "%s" is not valid - user was not found.',
                    $token->getCredentials(),
                    $username
                ));
            }
        }

        return $user;
    }
}

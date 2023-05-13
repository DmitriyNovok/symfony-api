<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\PayloadAwareUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtUserProvider implements PayloadAwareUserProviderInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->getUser('email', $identifier);
    }

    public function loadUserByIdentifierAndPayload(string $identifier, array $payload): UserInterface
    {
        return $this->getUser('id', $payload['id']);
    }

    /**
     * Load a user by its username, including the JWT token payload.
     *
     * @throws UsernameNotFoundException|UserNotFoundException if the user is not found
     *
     * @deprecated since 2.12, implement loadUserByIdentifierAndPayload() instead.
     */
    public function loadUserByUsernameAndPayload(string $username, array $payload): ?UserInterface
    {
        return null;
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     */
    public function refreshUser(UserInterface $user): ?UserInterface
    {
        return null;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @return bool
     */
    public function supportsClass(string $class)
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }

    /**
     * @deprecated since Symfony 5.3, use loadUserByIdentifier() instead
     */
    public function loadUserByUsername(string $username): ?UserInterface
    {
        return null;
    }

    private function getUser(string $key, string $value): UserInterface
    {
        $user = $this->userRepository->findOneBy([$key => $value]);

        if (null === $user) {
            $e = new UserNotFoundException('User with id '.json_encode($value).' not found.');
            $e->setUserIdentifier(json_encode($value));

            throw $e;
        }

        return $user;
    }
}

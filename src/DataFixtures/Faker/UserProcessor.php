<?php

namespace App\DataFixtures\Faker;

use App\Entity\User;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @codeCoverageIgnore
 */
class UserProcessor implements ProcessorInterface
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function preProcess(string $id, object $object): void
    {
        if ($object instanceof User && $object->getPlainPassword()) {
            $hashed = $this->hasher->hashPassword($object, $object->getPlainPassword());
            $object->setPassword($hashed);
        }
    }

    public function postProcess(string $id, object $object): void
    {
        // Rien à faire ici
    }
}

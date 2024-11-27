<?php declare(strict_types=1);

namespace App\Builder;

use App\DTO\UserDTO;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserEntityBuilder
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function buildFromDTO(UserDTO $userDTO): User
    {
        $user = new User();
        $user
            ->setEmail($userDTO->email)
            ->setFirstName($userDTO->firstName)
            ->setLastName($userDTO->lastName);

        $user->setPassword($this->hasher->hashPassword($user, $userDTO->password));

        return $user;
    }
}

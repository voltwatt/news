<?php declare(strict_types=1);

namespace App\Manager;

use App\Builder\UserEntityBuilder;
use App\DTO\DTOInterface;
use App\DTO\UserDTO;
use App\Serializer\AccessGroup;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use ReflectionException;

final readonly class UserManager
{
    use AutoMapper;

    public function __construct(private UserEntityBuilder $userEntityBuilder, private EntityManagerInterface $em)
    {
    }

    /**
     * @throws ReflectionException
     * @throws RandomException
     */
    public function create(UserDTO $userDTO): DTOInterface
    {
        $user = $this->userEntityBuilder->buildFromDTO($userDTO);
        $this->em->persist($user);
        $this->em->flush();

        return $this->mapToModel($user, AccessGroup::USER_READ);
    }
}
